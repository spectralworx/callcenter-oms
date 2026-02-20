<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Order;
use App\Services\WooCommerceClient;
use Illuminate\Http\Request;

class CallCentarController extends Controller
{
    /**
     * Call centar landing (nema liste, nema pretrage)
     * Uputstvo: koristi pickup scan za kompletiranje.
     */
    public function index()
    {
        return view('app.call-centar.index');
    }

    /**
     * Prikaz porudžbine (detalji + modal).
     */
    public function show(Order $order)
    {
        $order->load('items');

        return view('app.call-centar.show', [
            'order' => $order,
        ]);
    }

    /**
     * OTKAZ porudžbine (call centar).
     */
    public function cancel(Request $request, Order $order, WooCommerceClient $woo)
    {
        if ($order->status === 'completed') {
            return back()->withErrors([
                'status' => 'Ne možeš otkazati porudžbinu jer je već completed.',
            ]);
        }

        if ($order->status === 'cancelled') {
            return back()->with('status', 'Porudžbina je već otkazana.');
        }

        $order->update(['status' => 'cancelled']);

        $wooOk = null;
        $wooError = null;

        // Pokušaj Woo update samo ako je konfigurisan
        if ($woo->isConfigured()) {
            try {
                $wooOk = $woo->updateOrderStatus($order->woo_order_id, 'cancelled');
            } catch (\Throwable $e) {
                $wooOk = false;
                $wooError = $e->getMessage();
            }
        }

        AuditLog::create([
            'actor' => 'callcenter',
            'action' => 'cancel_order',
            'woo_order_id' => $order->woo_order_id,
            'meta' => [
                'order_id' => $order->id,
                'woo_attempted' => $woo->isConfigured(),
                'woo_ok' => $wooOk,
                'woo_error' => $wooError,
            ],
            'ip' => (string) $request->ip(),
        ]);

        if ($woo->isConfigured() && $wooOk === false) {
            return redirect()
                ->route('app.call-centar.show', $order)
                ->with('status', 'Otkazano lokalno, ali Woo update nije uspeo (pogledaj log).');
        }

        return redirect()
            ->route('app.call-centar.show', $order)
            ->with('status', 'Porudžbina otkazana.');
    }

    /**
     * OPTIONAL: Ako ipak želiš da call centar može da klikne "complete" (ja bih to držao samo u pickup scan).
     * Možeš da izbrišeš ovu metodu + rutu + dugme iz blade-a.
     */
    public function complete(Request $request, Order $order, WooCommerceClient $woo)
    {
        if ($order->status === 'cancelled') {
            return back()->withErrors([
                'status' => 'Ne možeš kompletirati pickup jer je porudžbina otkazana.',
            ]);
        }

        if ($order->status === 'completed') {
            return back()->with('status', 'Porudžbina je već completed.');
        }

        $order->update(['status' => 'completed']);

        $wooOk = null;
        $wooError = null;

        if ($woo->isConfigured()) {
            try {
                $wooOk = $woo->updateOrderStatus($order->woo_order_id, 'completed');
            } catch (\Throwable $e) {
                $wooOk = false;
                $wooError = $e->getMessage();
            }
        }

        AuditLog::create([
            'actor' => 'callcenter',
            'action' => 'complete_pickup',
            'woo_order_id' => $order->woo_order_id,
            'meta' => [
                'order_id' => $order->id,
                'termal_code' => $order->termal_code,
                'woo_attempted' => $woo->isConfigured(),
                'woo_ok' => $wooOk,
                'woo_error' => $wooError,
            ],
            'ip' => (string) $request->ip(),
        ]);

        if ($woo->isConfigured() && $wooOk === false) {
            return redirect()
                ->route('app.call-centar.show', $order)
                ->with('status', 'Pickup je označen completed lokalno, ali Woo update nije uspeo (pogledaj log).');
        }

        return redirect()
            ->route('app.call-centar.show', $order)
            ->with('status', 'Pickup označen kao completed.');
    }
}