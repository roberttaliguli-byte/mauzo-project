<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

public function build()
{
    $verifyUrl = route('verify.email', ['token' => $this->user->email_verification_token]);
                 
    return $this->subject('Verify Your Account')
                ->markdown('emails.verify')
                ->with([
                    'name' => $this->user->name,
                    'verifyUrl' => $verifyUrl,
                ]);
}

}
