<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class NewUserRegistrationNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $company;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->company = $user->company;
    }

    public function build()
    {
        return $this->subject('New User Registration - MauzoSheet')
                    ->markdown('emails.admin.new-user-notification')
                    ->with([
                        'user' => $this->user,
                        'company' => $this->company,
                    ]);
    }
}