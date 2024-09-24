<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebaseApprenantFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'firebase-apprenant';  // Nom de l'alias pour accéder au service réel
    }
}
