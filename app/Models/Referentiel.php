<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\FirebaseModel;
use App\Enums\ReferentielStatut;

class Referentiel extends FirebaseModel
{
    protected $connection = 'firebase';
    protected $collection = 'referentiels';

    protected $fillable = [
        'id', 'code', 'libelle', 'description', 'photo_couverture', 'statut', 'competences','modules'
    ];

    protected $casts = [
        'competences' => 'array',
        'modules' => 'array',
    ];

    public static function getStatuts(): array
    {
        return ReferentielStatut::values();
    }

}