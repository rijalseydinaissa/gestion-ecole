<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UserController;
use App\Http\Controllers\ReferentielController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ApprenantController;
use App\Http\Controllers\AuthController;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function (){
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
});

Route::prefix('v1/users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');  
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::patch('/{id}', [UserController::class, 'update'])->name('update');
    Route::get('/export', [UserController::class, 'export'])->name('users.export');
    Route::post('/import', [UserController::class, 'importUsers'])->name('users.import');

});

Route::prefix('v1/referentiels')->group(function (){
    Route::get('/', [ReferentielController::class, 'index'])->name('referentiels.index');  
    Route::post('/', [ReferentielController::class,'store'])->name('referentiels.store');
    Route::patch('/{id}', [ReferentielController::class, 'update'])->name('referentiels.update');
    Route::get('/{id}', [ReferentielController::class, 'show'])->name('referentiels.show');
    Route::delete('/{id}', [ReferentielController::class, 'destroy'])->name('referentiels.destroy');
    Route::get('/archive/referentiels', [ReferentielController::class, 'getAll']);

});

Route::prefix('v1/promotions')->group(function () {
    Route::get('/', [PromotionController::class, 'index'])->name('promotions.index');  
    Route::post('/', [PromotionController::class, 'store'])->name('promotions.store');
    Route::patch('/{id}', [PromotionController::class, 'update'])->name('promotions.update');
    Route::get('/{id}', [PromotionController::class,'show'])->name('promotions.show');
    Route::delete('/{id}', [PromotionController::class, 'destroy'])->name('promotions.destroy');
    Route::get('/encours', [PromotionController::class, 'getActifPromotion'])->name('promotions.actif');
    Route::get('/{id}/referentiels', [PromotionController::class, 'getReferentielsActifs'])->name('promotions.referentiels');
    Route::patch('/{id}/etat', [PromotionController::class, 'updateEtat']);
    Route::get('/{id}/stats', [PromotionController::class, 'getStats'])->name('promotions.stats');
    Route::patch('/{id}/referentiels', [PromotionController::class, 'updateReferentiel'])->name('promotions.ajoutouretire');
    Route::get('/export', [PromotionController::class, 'export'])->name('promotions.export');

    
});


Route::prefix('v1/apprenants')->group(function () {
    Route::get('/', [ApprenantController::class, 'index']);
    Route::post('/', [ApprenantController::class, 'store']);
    Route::get('/{id}', [ApprenantController::class, 'show']);
    Route::put('/{id}', [ApprenantController::class, 'update']);
    Route::delete('/{id}', [ApprenantController::class, 'delete']);
    Route::get('/active', [ApprenantController::class, 'getActiveApprenant']);
    Route::put('/deactivate', [ApprenantController::class, 'deactivateOtherApprenants']);
});

// ->middleware(['auth:api'])
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
