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

//Profile routes

Route::group(['middleware' => ['json.response']], function () {
    
    Route::post('v1/trips/create', [App\Http\Controllers\TripsController::class, 'create'])->name('create-trip.api');
  
});