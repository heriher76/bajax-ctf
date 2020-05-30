<?php
 
namespace App\Http\Middleware;
 
use Closure;
 
class RateLimits extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        $key = $this->resolveRequestSignature($request);

        $maxAttempts = $this->resolveMaxAttempts($request, $maxAttempts);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts, $decayMinutes)) {
            return $this->buildException($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes);

        $response = $next($request);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    protected function resolveRequestSignature($request)
    {
        return sha1(implode('|', [
          $request->method(),
          $request->root(),
          $request->path(),
          $request->ip()
        ]));

        return $request->fingerprint();
    }

    /**
     * Create a 'too many attempts' exception.
     *
     * @param  string  $key
     * @param  int  $maxAttempts
     * @return \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function buildException($key, $maxAttempts)
    {
        $retryAfter = $this->getTimeUntilNextRetry($key);

        $headers = $this->getHeaders(
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
        return response()->json([
            'success' => false,
            'messages' => 'Too Many Attempts.!',
            'data' => NULL,
        ], 429);
    }
}


