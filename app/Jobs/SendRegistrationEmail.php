<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationEmail;

class SendRegistrationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $emailData;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param array $emailData
     * @param array $userId
     */
    public function __construct($userId, array $emailData)
    {
        $this->userId = $userId;
        $this->emailData = $emailData;
    }
    
    public function handle()
    {
        $user = User::find($this->userId);
    
        if ($user) {
            // Envoi de l'email avec les informations d'inscription
            Mail::to($user->email)->send(new RegistrationEmail($user, $this->emailData));
        }
    }
    
    
}


