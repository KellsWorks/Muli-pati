<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Authentication routes

Route::group(['middleware' => ['cors', 'json.response']], function () {
    
    Route::post('v1/login', [App\Http\Controllers\Auth\ApiAuthController::class, 'login'])->name('login.api');
    Route::post('v1/register',[App\Http\Controllers\Auth\ApiAuthController::class, 'register'])->name('register.api');
    Route::post('v1/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout'])->name('logout.api');

    Route::post('v1/update-photo/{id}', [App\Http\Controllers\Auth\ApiAuthController::class, 'updatePhoto'])->name('updatePhoto.api');

});

Route::middleware('auth:api')->group(function () {

    Route::post('v1/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout'])->name('logout.api');

});

//trips routes

Route::group(['middleware' => ['json.response']], function () {
    
    Route::post('v1/trips/create/{id}', [App\Http\Controllers\TripsController::class, 'create']);

    Route::get('v1/trips/all', [App\Http\Controllers\TripsController::class, 'trips']);

    Route::post('v1/trips/book', [App\Http\Controllers\TripsController::class, 'book']);

    Route::post('v1/trips/completed', [App\Http\Controllers\TripsController::class, 'completed']);
  
    Route::post('v1/trips/cancelled', [App\Http\Controllers\TripsController::class, 'cancelled']);

    Route::post('v1/trips/delete', [App\Http\Controllers\TripsController::class, 'delete']);

    Route::post('v1/trips/update', [App\Http\Controllers\TripsController::class, 'update']);
  
});