<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebaseReferentielFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'referentiel-facade';
    }
}