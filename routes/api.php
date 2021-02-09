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


    Route::post('v1/users',[App\Http\Controllers\Auth\ApiAuthController::class, 'getUsers'])->name('getUsers.api');


    //admin register
    Route::post('v1/register-admin',[App\Http\Controllers\Auth\ApiAuthController::class, 'registerAgent'])->name('registerAgent.api');

    Route::post('v1/logout', [App\Http\Controllers\Auth\ApiAuthController::class, 'logout'])->name('logout.api');

    Route::post('v1/delete', [App\Http\Controllers\Auth\ApiAuthController::class, 'delete'])->name('delete.api');


    Route::post('v1/update-photo', [App\Http\Controllers\Auth\ApiAuthController::class, 'updatePhoto'])->name('updatePhoto.api');

    Route::post('v1/update-location', [App\Http\Controllers\Auth\ApiAuthController::class, 'updateLocation'])->name('updateLocation.api');

    Route::post('v1/update-account', [App\Http\Controllers\Auth\ApiAuthController::class, 'updateAccount'])->name('updateAccount.api');

});

//subscriptions routes

Route::group(['middleware' => ['json.response']], function () {

    Route::post('v1/subscription/subscribe', [App\Http\Controllers\SubscriptionsController::class, 'subscribe']);
    Route::get('v1/subscription/end-subscription', [App\Http\Controllers\SubscriptionsController::class, 'endSubscription']);

});

//FCM

Route::group(['middleware' => ['json.response']], function () {

    Route::post('v1/fcm-token/save', [App\Http\Controllers\FCMController::class, 'saveToken']);

});


//trips routes

Route::group(['middleware' => ['json.response']], function () {

    Route::post('v1/trips/create', [App\Http\Controllers\TripsController::class, 'create']);

    Route::get('v1/trips/all', [App\Http\Controllers\TripsController::class, 'allTrips']);

    Route::post('v1/trips/bookings', [App\Http\Controllers\TripsController::class, 'getBookings']);

    Route::post('v1/trips/completed', [App\Http\Controllers\TripsController::class, 'completed']);

    Route::post('v1/trips/cancelled', [App\Http\Controllers\TripsController::class, 'cancelled']);

    Route::post('v1/trips/delete', [App\Http\Controllers\TripsController::class, 'delete']);

    Route::post('v1/trips/update', [App\Http\Controllers\TripsController::class, 'update']);

    Route::post('v1/trips/trip-status', [App\Http\Controllers\TripsController::class, 'statusDelete']);


    Route::get('v1/trips/trips/all', [App\Http\Controllers\TripsController::class, 'allTrips']);

    Route::get('v1/trips/trips/all/{location}', [App\Http\Controllers\TripsController::class, 'allTripsLocation']);

    //User bookings
    Route::post('v1/trips/book-trip', [App\Http\Controllers\BookingsController::class, 'create']);
    Route::post('v1/trips/user-trips', [App\Http\Controllers\BookingsController::class, 'getBookedTrips']);
    Route::post('v1/trips/user-cancelled-trips', [App\Http\Controllers\BookingsController::class, 'getCancelledTrips']);
    Route::post('v1/trips/user-bookings', [App\Http\Controllers\BookingsController::class, 'getUserBookings']);
    Route::post('v1/trips/cancel-trip', [App\Http\Controllers\BookingsController::class, 'cancel']);

});

// Messaging routes

Route::group(['middleware' => ['json.response']], function () {

    Route::post('v1/message/create', [App\Http\Controllers\MessagingController::class, 'create']);

    Route::post('v1/message/get-messages', [App\Http\Controllers\MessagingController::class, 'getMessages']);

    Route::get('v1/message/delete', [App\Http\Controllers\MessagingController::class, 'delete']);

});

//User notifications

Route::group(['middleware' => ['json.response']], function () {

    Route::post('v1/notifications/user-notifications', [App\Http\Controllers\NotificationsController::class, 'userNotification']);
    Route::post('v1/notifications/user-mark-notification', [App\Http\Controllers\NotificationsController::class, 'markAsRead']);
    Route::post('v1/notifications/user-notification-delete', [App\Http\Controllers\NotificationsController::class, 'delete']);

});
