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

Route::group(['middleware' => ['cors', 'json.response']], function () {
    
    Route::post('v1/login', [App\Http\Controllers\Auth\ApiAuthController::class, 'login'])->name('login.api');
    Route::post('v1/register',[App\Http\Controllers\Auth\ApiAuthController::class, 'register'])->name('register.api');
    Route::post('v1/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout'])->name('logout.api');

});

Route::middleware('auth:api')->group(function () {

    Route::post('v1/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout'])->name('logout.api');

});
