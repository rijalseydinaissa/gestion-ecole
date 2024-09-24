<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FirebaseModel;

class Promotion extends FirebaseModel
{
    protected $connection = 'firebase';
    protected $table = 'promotions';
    protected $fillable = [
        'libelle', 'date_debut', 'date_fin', 'duree', 'etat', 'photo_couverture', 'referentiels'
    ];
    
}
