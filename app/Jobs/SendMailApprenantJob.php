<?php

namespace App\Jobs;

use Mail; // Assurez-vous d'importer la façade Mail
use App\Mail\AuthenticationEmail; // Importez la classe de mail
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailApprenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $email;
    protected string $matricule;
    protected string $password;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param string $matricule
     * @param string $password
     */
    public function __construct(string $email, string $matricule, string $password)
    {
        $this->email = $email;
        $this->matricule = $matricule;
        $this->password = $password;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Envoyer l'email d'authentification
        Mail::to($this->email)->send(new AuthenticationEmail($this->email, $this->password));
    }
}
