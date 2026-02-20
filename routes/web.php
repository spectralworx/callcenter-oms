<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\WooWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ovde definišemo rute za tvoj API. Laravel 11/12 ove rute automatski
| stavlja pod "/api" prefiks. Dakle, putanja će biti:
| tvoj-domen.com/api/webhooks/woocommerce
|
*/

// Webhook ruta za WooCommerce
// Primenjujemo 'woo.webhook.hmac' alias koji smo dodali u bootstrap/app.php
Route::post('/webhooks/woocommerce', [WooWebhookController::class, 'handle'])
    ->middleware('woo.webhook.hmac')
    ->name('api.webhooks.woocommerce');

// Primer rute za proveru statusa (opciono)
Route::get('/status', function () {
    return response()->json(['status' => 'online', 'version' => '1.0.0']);
});