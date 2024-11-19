<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('verify-request', 'verifyRequest');
    Route::post('verify', 'verify');
    Route::post('resend', 'verifyRequest');
    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('update', 'update');
        Route::get('profile', 'profile');
        Route::get('logout', 'logout');
    });
});
