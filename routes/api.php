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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


//Auth
// Public Routes
Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::post('/forgot-password', 'AuthController@forgotPassword')->name('password.email');
Route::post('/reset-password', 'AuthController@resetPassword')->name('password.reset');

// Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
//     ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
//     ->name('verification.verify');

Route::post('/email/verification-notification', 'AuthController@sendVerificationEmail')->middleware(['throttle:6,1']);
Route::get('/email/verify/{id}/{hash}', 'AuthController@verifyEmail')->name('verification.verify')->middleware(['signed', 'throttle:6,1']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', 'AuthController@logout');
    // Add more protected routes here
});




// Event Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/events', 'EventController@index');
    Route::post('/events', 'EventController@store');
    Route::get('/events/{event}', 'EventController@show');
    Route::put('/events/{event}', 'EventController@update');
    Route::delete('/events/{event}', 'EventController@destroy');

    // Media Upload Routes
    Route::post('/events/{event}/cover-image', 'EventController@uploadCoverImage');
    Route::post('/events/{event}/media', 'MediaController@store');
    Route::delete('/media/{media}', 'MediaController@destroy');

    // Billing/Payment Routes
    Route::post('/events/{event}/payments', 'PaymentController@store');
    Route::get('/events/{event}/payments', 'PaymentController@show');
});

// Public Access Routes (No Auth Required)
Route::get('/events/{event}/gallery', 'EventController@gallery');

