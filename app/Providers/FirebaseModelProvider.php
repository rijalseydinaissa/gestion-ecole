<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceInterface;
use App\Repository\FirebaseRepository;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseModelProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Database::class, function ($app) {
            $firebaseCredentiels= base64_decode(config('firebase.credentials'));
            return (new Factory)
                ->withServiceAccount(json_decode($firebaseCredentiels,true) )
                ->withDatabaseUri(config('firebase.database_url'))
                ->createDatabase();
        });

        $this->app->singleton(FirebaseRepository::class, function ($app) {
            return new FirebaseRepository($app->make(Database::class));
        });

        $this->app->singleton(FirebaseServiceInterface::class, function ($app) {
            return new FirebaseService($app->make(FirebaseRepository::class));
        });

        $this->app->alias(FirebaseServiceInterface::class, 'firebase');
    }

    public function boot()
    {
        //
    }
}