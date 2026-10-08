<?php

use App\Http\Controllers\Webhook\ComposioGmailWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/composio/gmail-payment', [ComposioGmailWebhookController::class, 'handle'])
    ->name('webhook.composio.gmail');