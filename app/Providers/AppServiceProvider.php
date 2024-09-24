<?php

namespace App\Providers;

use App\Facades\FirebasePromotionFacade;
use App\Services\FirebaseService;
use App\Services\UserServiceInterface;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use App\Services\FirebaseServiceInterface;
use App\Repository\FirebaseRepository;
use App\Services\FirebaseUserService;
use App\Facades\FirebaseUserFacade;
use App\Facades\FirebaseApprenantFacade;
use App\Facades\FirebaseReferentielFacade;
use App\Repository\FirebaseApprenantRepository;
use App\Services\FirebaseApprenantService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistre FirebaseRepository avec l'instance de Database Firebase
        $this->app->singleton(FirebaseRepository::class, function ($app) {
            $firebase = (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDatabaseUri(config('firebase.database_url'));

            return new FirebaseRepository($firebase->createDatabase());
        });

        // Enregistre FirebaseService et son interface
        $this->app->singleton(FirebaseServiceInterface::class, function ($app) {
            return new FirebaseService($app->make(FirebaseRepository::class));
        });
        $this->app->bind(UserServiceInterface::class, FirebaseUserService::class);
        $this->app->alias(FirebaseServiceInterface::class, 'firebase');
        $this->app->bind('user-facade', function($app) {
            return new FirebaseUserFacade();
        });
        $this->app->bind('referentiel-facade', function($app) {
            return new FirebaseReferentielFacade();
        });
        $this->app->bind('promotion-facade', function($app) {
            return new FirebasePromotionFacade();
        });
        $this->app->bind('apprenant-facade', function($app) {
            return new FirebaseApprenantFacade();
        });
        $this->app->singleton(FirebaseApprenantRepository::class, function ($app) {
            return new FirebaseApprenantRepository();
        });
    
        $this->app->singleton(FirebaseApprenantService::class, function ($app) {
            return new FirebaseApprenantService($app->make(FirebaseApprenantRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
