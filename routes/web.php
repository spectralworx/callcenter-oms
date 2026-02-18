<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Webhook\WooWebhookController;
use App\Jobs\ProcessIncomingEvent;
use App\Models\IncomingEvent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Webhook (Woo -> Laravel)
Route::post('/api/webhooks/woocommerce', [WooWebhookController::class, 'handle'])
    ->name('webhooks.woocommerce');


// ✅ DEV TEST (bez Postmana) — radi samo u local okruženju
// Ako hoćeš da radi i na Railway dok testiraš, privremeno stavi APP_ENV=staging
Route::get('/dev/test-webhook', function () {

    // Dozvoli samo u local (ili dodaj 'staging' dok testiraš)
    if (!App::environment(['local'])) {
        abort(404);
    }

    $payload = [
        'event_type' => 'order.upsert',
        'order' => [
            'woo_order_id' => 'TEST-1',
            'order_number' => 'TEST-ORDER-1',
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+38160000000',
            'email' => 'test@example.com',
            'address' => 'Test ulica 1',
            'city' => 'Beograd',
            'postcode' => '11000',
            'total' => 1000,
            'tax_total' => 166.67,
            'currency' => 'RSD',
            'status' => 'processing',
            'termal_code' => '1234567',
            'tracking_numbers' => ['GLS123456'],
            'office_notice' => 'TEST NOTICE',
        ],
        'items' => [
            [
                'name' => 'Proizvod A',
                'qty' => 1,
                'sku' => 'SKU-TEST-A',
                'ean' => '8600000000001',
                'line_total' => 1000,
                'line_tax' => 166.67,
            ],
        ],
    ];

    $event = IncomingEvent::create([
        'source' => 'dev',
        'event_type' => 'order.upsert',
        'external_id' => 'TEST-1',
        'dedupe_key' => 'dev-test-1-' . now()->timestamp, // da uvek kreira novi
        'payload' => $payload,
        'status' => 'queued',
    ]);

    ProcessIncomingEvent::dispatch($event->id);

    return response()->json([
        'ok' => true,
        'message' => 'Dispatched async job',
        'incoming_event_id' => $event->id,
    ]);
});

require __DIR__ . '/auth.php';
