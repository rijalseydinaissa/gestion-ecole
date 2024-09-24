<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use App\Models\FirebaseModel;
use App\Facades\UserFacade;
use App\Enums\UserRole;

class Apprenant extends FirebaseModel
{
    use Notifiable;

    protected $connection = 'firebase';
    protected $collection = 'apprenants';

    protected $fillable = [
        'id', 'nom', 'prenom', 'adresse', 'telephone','password', , 'email', 'photo', 'statut', 'role','referentiel'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getRoles(): array
    {
        return UserRole::values();
    }

    // Vous pouvez ajouter des méthodes spécifiques ici si nécessaire
}
