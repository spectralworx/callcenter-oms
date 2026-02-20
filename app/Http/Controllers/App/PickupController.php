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
        return view('app.pickup.index');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:60'],
        ]);

        $code = trim((string) $validated['code']);

        $order = Order::query()
            ->where('termal_code', $code)
            ->orderByDesc('id')
            ->first();

        if (! $order) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Nije pronađena porudžbina za ovaj kod.']);
        }

        // Flag da show strana zna da automatski otvori pickup modal
        return redirect()->route('app.call-centar.show', $order->id) . '?pickup=1';
    }
}