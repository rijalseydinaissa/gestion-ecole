<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebasePromotionFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        // Le nom de l'alias qui sera utilisé dans le container de services Laravel
        return 'promotion-facade';
    }
}
