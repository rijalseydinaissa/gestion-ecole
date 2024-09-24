<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthenticationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public string $password;

    /**
     * Create a new message instance.
     *
     * @param string $matricule
     * @param string $password
     */
    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Informations d\'authentification')
            ->view('emails.authentication') // Spécifiez la vue pour l'e-mail
            ->with([
                'email' => $this->email,
                'password' => $this->password,
            ]);
    }
}

