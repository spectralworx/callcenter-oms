<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\WooWebhookController;

Route::post('/webhooks/woocommerce/order', [WooWebhookController::class, 'order'])
    ->middleware([
        'throttle:60,1',       // rate limit (podesi kasnije)
        'woo.webhook.hmac',    // HMAC middleware (dodajemo dole)
        // 'ip.allowlist:webhook', // opcionalno
    ]);