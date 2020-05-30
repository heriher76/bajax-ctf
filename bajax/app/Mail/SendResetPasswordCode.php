<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class SendResetPasswordCode extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }
    public function build()
    {
        return $this->to($this->user->email, $this->user->name)
            ->subject('Reset Password')
            ->view('mails.forgotpassword')
            ->with('url', $this->url);
    }
}