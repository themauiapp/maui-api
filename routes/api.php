<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
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

Route::middleware(['auth:sanctum', 'signed'])->post('/email/verify/{id}/{hash}',
function(EmailVerificationRequest $request) {
    $user = $request->user();
    date_default_timezone_set($user->timezone);
    $request->fulfill();
    return [
        'message' => 'email verified successfully',
        'user' => $user
    ];
})->name('verification.verify');
