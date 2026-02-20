<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CallCentarController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $printed = $request->query('printed', '');
        $from = $request->query('from'); // YYYY-MM-DD
        $to = $request->query('to');     // YYYY-MM-DD

        $orders = Order::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('woo_order_id', 'like', "%{$q}%")
                        ->orWhere('order_number', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('termal_code', 'like', "%{$q}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($printed === '1', fn ($query) => $query->where('is_printed', true))
            ->when($printed === '0', fn ($query) => $query->where('is_printed', false))
            ->when($from, fn ($query) => $query->whereDate('created_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('created_at', '<=', $to))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        // za dropdown statusa:
        $statuses = Order::query()
            ->select('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->values();

        return view('app.call-centar.index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'filters' => [
                'q' => $q,
                'status' => $status,
                'printed' => $printed,
                'from' => $from,
                'to' => $to,
            ],
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['items']);

        return view('app.call-centar.show', [
            'order' => $order,
        ]);
    }
}