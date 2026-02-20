<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\WooWebhookController;

Route::post('/webhooks/woocommerce/order', [WooWebhookController::class, 'handle'])
    ->middleware([
        'throttle:60,1',
        'woo.webhook.hmac',
    ])
    ->name('webhooks.woocommerce.order');