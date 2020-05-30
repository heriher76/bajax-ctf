<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

class SendEMailVerification extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $url;

    public function __construct(User $user, $url)
    {
        $this->user = $user;
        $this->url = $url;
    }
    public function build()
    {
        return $this->to($this->user->email, $this->user->name)
            ->subject('Verify Your Email Address')
            ->view('mails.emailverification')
            ->with('url', $this->url);
    }
}