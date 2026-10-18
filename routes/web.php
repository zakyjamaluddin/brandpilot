<?php

use Illuminate\Support\Facades\Route;

// routes/web.php

Route::get('/', function () {
    return view('landing');
});

use App\Http\Controllers\ComposioCallbackController;

Route::get('/composio/callback/{team}/{platform}', [ComposioCallbackController::class, 'handle'])
    ->name('composio.callback')
    ->middleware(['web', 'auth']);

// routes/web.php
use App\Http\Controllers\WebPushSubscriptionController;

Route::post('/webpush/subscribe', [WebPushSubscriptionController::class, 'store'])
    ->middleware(['web', 'auth'])
    ->name('webpush.subscribe');