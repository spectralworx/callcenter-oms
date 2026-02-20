<?php

use App\Http\Controllers\Auth\PinLoginController;
use App\Http\Controllers\App\HomeController;
use App\Http\Controllers\App\CallCentarController;
use App\Http\Controllers\App\PickupController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Početna strana -> PIN login
Route::get('/', function () {
    return redirect()->route('pin.login');
});

// --- PIN Authentication ---
Route::get('/login', [PinLoginController::class, 'create'])->name('pin.login');
Route::post('/login', [PinLoginController::class, 'store'])->name('pin.login.store');
Route::post('/logout', [PinLoginController::class, 'destroy'])->name('logout');

// --- App (zaštićeno) ---
Route::middleware(['auth'])->group(function () {

    Route::get('/app', [HomeController::class, 'index'])->name('app.home');

    // Call Centar
    Route::get('/app/call-centar', [CallCentarController::class, 'index'])->name('app.call-centar');
    Route::get('/app/call-centar/{order}', [CallCentarController::class, 'show'])->name('app.call-centar.show');
    Route::post('/app/call-centar/{order}/cancel', [CallCentarController::class, 'cancel'])->name('app.call-centar.cancel');
    Route::post('/app/call-centar/{order}/complete', [CallCentarController::class, 'complete'])->name('app.call-centar.complete');

    // Pickup (scan -> open order)
    Route::get('/app/licno-preuzimanje', [PickupController::class, 'index'])->name('app.pickup');
    Route::post('/app/licno-preuzimanje', [PickupController::class, 'scan'])->name('app.pickup.scan');

    // Ostalo
    Route::view('/app/posalji-porudzbinu', 'app.send.index')->name('app.send');
    Route::view('/app/stampa', 'app.print.index')->name('app.print');

    // Profil (opciono)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ne uključujemo auth.php jer ne koristimo email/password login
// require __DIR__.'/auth.php';