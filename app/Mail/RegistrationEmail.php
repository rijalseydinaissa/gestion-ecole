<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $emailData;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param array $emailData
     */
    public function __construct(User $user, array $emailData)
    {
        $this->user = $user;
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.registration')
                    ->with([
                        'login' => $this->emailData['login'],
                        'password' => $this->emailData['password'],
                        // 'auth_link' => $this->emailData['auth_link'],
                    ])
                    ->subject('Vos informations d\'inscription');
    }
}

