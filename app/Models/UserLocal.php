<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class UserLocal extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    public $incrementing = false; // Indique que l'ID n'est pas auto-incrémenté
    protected $keyType = 'string'; // Indique que la clé primaire est une chaîne de caractères

    protected $fillable = [
        'id', 'nom', 'prenom', 'adresse', 'telephone', 'password', 'fonction', 'email', 'photo', 'statut', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
