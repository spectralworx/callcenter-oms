<?php

use App\Http\Controllers\Auth\PinLoginController;
use App\Http\Controllers\App\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Webhook\WooWebhookController;
use Illuminate\Support\Facades\Route;

// Početna strana sada odmah preusmerava na PIN login
Route::get('/', function () {
    return redirect()->route('pin.login');
});

// --- PIN Authentication ---
Route::get('/login', [PinLoginController::class, 'create'])->name('pin.login');
Route::post('/login', [PinLoginController::class, 'store'])->name('pin.login.store');
Route::post('/logout', [PinLoginController::class, 'destroy'])->name('logout');

// --- App (Rute zaštićene prijavom) ---
Route::middleware(['auth'])->group(function () {
    
    // Glavna tabla (Home)
    Route::get('/app', [HomeController::class, 'index'])->name('app.home');

    // Moduli (Punićemo ih jedan po jedan kako programer bude slao kod)
    Route::view('/app/call-centar', 'app.call-centar.index')->name('app.call-centar');
    Route::view('/app/licno-preuzimanje', 'app.pickup.index')->name('app.pickup');
    Route::view('/app/posalji-porudzbinu', 'app.send.index')->name('app.send');
    Route::view('/app/stampa', 'app.print.index')->name('app.print');

    // Tvoje postojeće rute za profil (ako ti i dalje trebaju)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Webhook (Woo -> Laravel) - Ostaje van auth jer ga poziva WooCommerce spolja
Route::post('/api/webhooks/woocommerce', [WooWebhookController::class, 'handle'])
    ->name('webhooks.woocommerce');

// KOMENTAR: require __DIR__ . '/auth.php'; 
// Isključeno po instrukciji programera da ne bi ostao stari login.