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
