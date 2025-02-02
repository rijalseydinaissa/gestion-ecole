<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FirebaseUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user-facade';
    }
}