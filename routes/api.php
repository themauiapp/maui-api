<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;

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

Route::middleware(['auth:sanctum', 'signed'])->post('/email/verify/{id}/{hash}', function(EmailVerificationRequest $request) {
    $user = $request->user();
    date_default_timezone_set($user->timezone);
    $request->fulfill();
    return [
        'message' => 'email verified successfully',
        'user' => $user
    ];
})->name('verification.verify');

Route::get('/cli-tokens/{token}', function($token) {
    $cliAuthToken = DB::table('cli_auth_tokens')->where('cli_token', $token)->first();
    $authToken = $cliAuthToken ? $cliAuthToken->auth_token : NULL;

    if ($cliAuthToken) {
        DB::table('cli_auth_tokens')->where('id', $cliAuthToken->id)->delete();
    }

    return [
        'auth-token' => $authToken
    ];
});

Route::post('/logout', function() {
    Auth::logout();
    Cookie::queue(Cookie::forget('maui_cookie'));
    Cookie::queue(Cookie::forget('maui_token'));
    return [
        'message' => 'logged out successfully' 
    ];
});