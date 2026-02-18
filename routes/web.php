<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Webhook\WooWebhookController;
use App\Jobs\ProcessIncomingEvent;
use App\Models\IncomingEvent;
use Illuminate\Support\Facades\Route;
use App\Models\Order;

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


// ✅ DEV TEST (bez Postmana) — radi i na Railway, ali SAMO uz token
Route::get('/dev/test-webhook', function () {

    $token = (string) request()->query('token', '');
    $expected = (string) env('DEV_TEST_TOKEN', '');

    // Ako token nije podešen ili nije tačan -> 404 (da ne otkrivaš da ruta postoji)
    if ($expected === '' || $token === '' || !hash_equals($expected, $token)) {
        abort(404);
    }

    $payload = [
        'event_type' => 'order.upsert',
        'order' => [
            'woo_order_id' => 'TEST-' . now()->format('His'),
            'order_number' => 'TEST-ORDER-' . now()->format('His'),
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

    $dedupeKey = 'dev-test-' . now()->timestamp . '-' . bin2hex(random_bytes(4));

    $event = IncomingEvent::create([
        'source' => 'dev',
        'event_type' => 'order.upsert',
        'external_id' => data_get($payload, 'order.woo_order_id'),
        'dedupe_key' => $dedupeKey,
        'payload' => $payload,
        'status' => 'queued',
    ]);

    ProcessIncomingEvent::dispatch($event->id);

    return response()->json([
        'ok' => true,
        'message' => 'Dispatched async job',
        'incoming_event_id' => $event->id,
        'woo_order_id' => data_get($payload, 'order.woo_order_id'),
    ]);
});

Route::get('/dev/orders', function () {
    $token = (string) request()->query('token', '');
    $expected = (string) env('DEV_TEST_TOKEN', '');

    if ($expected === '' || $token === '' || !hash_equals($expected, $token)) {
        abort(404);
    }

    return Order::with('items')
        ->orderByDesc('id')
        ->take(10)
        ->get();
});

require __DIR__ . '/auth.php';
