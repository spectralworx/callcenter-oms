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
     * Lista porudžbina sa pretragom i filterom.
     */
    public function index(Request $request)
    {
        $q      = trim((string) $request->get('q', ''));
        $status = trim((string) $request->get('status', ''));

        $orders = Order::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('first_name',    'like', "%{$q}%")
                        ->orWhere('last_name',   'like', "%{$q}%")
                        ->orWhere('phone',       'like', "%{$q}%")
                        ->orWhere('email',       'like', "%{$q}%")
                        ->orWhere('order_number','like', "%{$q}%")
                        ->orWhere('termal_code', 'like', "%{$q}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $statuses = Order::distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        return view('app.call-centar.index', [
            'orders'   => $orders,
            'statuses' => $statuses,
            'filters'  => ['q' => $q, 'status' => $status],
        ]);
    }

    /**
     * Prikaz porudžbine (detalji + akcije).
     */
    public function show(Order $order)
    {
        $order->load('items');

        return view('app.call-centar.show', [
            'order' => $order,
        ]);
    }

    /**
     * OTKAZ porudžbine.
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

        $wooOk    = null;
        $wooError = null;

        if ($woo->isConfigured()) {
            try {
                $wooOk = $woo->updateOrderStatus($order->woo_order_id, 'cancelled');
            } catch (\Throwable $e) {
                $wooOk    = false;
                $wooError = $e->getMessage();
            }
        }

        AuditLog::create([
            'actor'        => auth()->user()?->name ?? 'callcenter',
            'action'       => 'cancel_order',
            'woo_order_id' => $order->woo_order_id,
            'meta'         => [
                'order_id'      => $order->id,
                'woo_attempted' => $woo->isConfigured(),
                'woo_ok'        => $wooOk,
                'woo_error'     => $wooError,
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
     * COMPLETE pickup (call centar).
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

        $wooOk    = null;
        $wooError = null;

        if ($woo->isConfigured()) {
            try {
                $wooOk = $woo->updateOrderStatus($order->woo_order_id, 'completed');
            } catch (\Throwable $e) {
                $wooOk    = false;
                $wooError = $e->getMessage();
            }
        }

        AuditLog::create([
            'actor'        => auth()->user()?->name ?? 'callcenter',
            'action'       => 'complete_pickup',
            'woo_order_id' => $order->woo_order_id,
            'meta'         => [
                'order_id'      => $order->id,
                'termal_code'   => $order->termal_code,
                'woo_attempted' => $woo->isConfigured(),
                'woo_ok'        => $wooOk,
                'woo_error'     => $wooError,
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
