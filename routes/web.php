<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ComposioCallbackController;

Route::get('/composio/callback/{team}/{platform}', [ComposioCallbackController::class, 'handle'])
    ->name('composio.callback')
    ->middleware(['web', 'auth']);
