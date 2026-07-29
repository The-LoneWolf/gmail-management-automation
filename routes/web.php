<?php

use App\Http\Controllers\GmailOAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/gmail/oauth/redirect', [GmailOAuthController::class, 'redirect'])->name('gmail.oauth.redirect');
    Route::get('/gmail/oauth/callback', [GmailOAuthController::class, 'callback'])->name('gmail.oauth.callback');
});
