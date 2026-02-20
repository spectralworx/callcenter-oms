<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PickupController extends Controller
{
    public function index(): View
    {
        // Poslednjih 20 completed porudžbina danas (za "Poslednja preuzimanja" panel)
        $recent = Order::query()
            ->where('status', 'completed')
            ->whereDate('updated_at', today())
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get(['id', 'order_number', 'woo_order_id', 'first_name', 'last_name', 'updated_at']);

        return view('app.pickup.index', [
            'recent' => $recent,
        ]);
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60'],
        ]);

        $code = trim((string) $validated['code']);

        // Ukloni # ako je scanner slučajno dodao
        $code = ltrim($code, '#');

        $order = Order::query()
            ->where('termal_code', $code)
            ->orderByDesc('id')
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors(['code' => "Nije pronađena porudžbina za kod: {$code}"]);
        }

        // Redirect na show stranicu sa ?pickup=1 da automatski fokusira akciju
        return redirect()->route('app.call-centar.show', [
            'order'  => $order->id,
            'pickup' => 1,
        ]);
    }
}
