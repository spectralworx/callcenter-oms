<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Porudžbina #{{ $order->order_number ?? $order->woo_order_id }} – OMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Figtree',sans-serif; background:#f8fafc; min-height:100vh; display:flex; flex-direction:column; }

        .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 2rem; height:58px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
        .topbar-left { display:flex; align-items:center; gap:1rem; }
        .back-btn { display:inline-flex; align-items:center; gap:0.4rem; text-decoration:none; font-size:0.85rem; font-weight:700; color:#64748b; padding:0.4rem 0.75rem; border:1px solid #e2e8f0; border-radius:8px; transition:all 0.1s; }
        .back-btn:hover { background:#f1f5f9; color:#334155; }
        .topbar-title { font-weight:800; font-size:1.05rem; color:#1e293b; }
        .topbar-user { font-size:0.85rem; color:#64748b; font-weight:600; }

        .badge { display:inline-flex; align-items:center; padding:0.2rem 0.65rem; border-radius:99px; font-size:0.72rem; font-weight:700; }
        .badge-gray  { background:#f1f5f9; color:#475569; }
        .badge-green { background:#dcfce7; color:#15803d; }
        .badge-amber { background:#fef3c7; color:#b45309; }
        .badge-red   { background:#fee2e2; color:#dc2626; }

        .content { flex:1; overflow-y:auto; padding:1.25rem 2rem; }
        .layout { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; max-width:1200px; }

        .card { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; padding:1.25rem 1.5rem; }
        .card + .card { margin-top:1rem; }
        .card-label { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#94a3b8; margin-bottom:0.5rem; }

        .customer-name { font-size:1.5rem; font-weight:800; color:#1e293b; letter-spacing:-0.02em; }
        .customer-phone { font-size:1.1rem; font-weight:700; color:#4f46e5; margin-top:0.25rem; }
        .customer-detail { font-size:0.85rem; color:#64748b; font-weight:500; margin-top:0.15rem; }

        .total-amount { font-size:2rem; font-weight:900; color:#1e293b; letter-spacing:-0.03em; }
        .total-label { font-size:0.8rem; color:#94a3b8; font-weight:600; }

        .office-notice { background:#fffbeb; border:1.5px solid #fde68a; border-radius:12px; padding:1rem 1.25rem; margin-top:1rem; }
        .office-notice .notice-label { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#b45309; margin-bottom:0.35rem; }
        .office-notice .notice-text { font-size:0.9rem; color:#78350f; font-weight:600; white-space:pre-wrap; }

        .items-table { width:100%; border-collapse:collapse; font-size:0.88rem; }
        .items-table th { text-align:left; padding:0.5rem 0.5rem; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; border-bottom:1px solid #f1f5f9; }
        .items-table td { padding:0.65rem 0.5rem; border-bottom:1px solid #f8fafc; color:#334155; font-weight:600; vertical-align:top; }
        .items-table tr:last-child td { border-bottom:none; }
        .item-name { font-weight:700; color:#1e293b; }
        .item-sku { font-size:0.75rem; color:#94a3b8; font-weight:500; }

        /* ACTION BUTTONS */
        .action-card { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; overflow:hidden; }
        .action-card + .action-card { margin-top:1rem; }
        .action-header { padding:0.9rem 1.25rem; border-bottom:1px solid #f1f5f9; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#94a3b8; }
        .action-body { padding:1rem 1.25rem; }

        .action-textarea { width:100%; border:1.5px solid #e2e8f0; border-radius:10px; padding:0.65rem 0.9rem; font-size:0.88rem; font-family:inherit; color:#334155; font-weight:500; resize:none; outline:none; transition:border-color 0.12s; }
        .action-textarea:focus { border-color:#818cf8; }
        .action-textarea::placeholder { color:#94a3b8; }

        .action-btn { display:block; width:100%; margin-top:0.75rem; padding:0.85rem; border:none; border-radius:10px; font-size:1rem; font-weight:800; font-family:inherit; cursor:pointer; transition:all 0.1s; letter-spacing:-0.01em; }
        .action-btn:active { transform:scale(0.98); }
        .btn-cancel   { background:#fef2f2; color:#dc2626; border:1.5px solid #fecaca; }
        .btn-cancel:hover { background:#fee2e2; border-color:#f87171; }
        .btn-complete { background:#f0fdf4; color:#15803d; border:1.5px solid #bbf7d0; }
        .btn-complete:hover { background:#dcfce7; border-color:#4ade80; }
        .btn-disabled { opacity:0.4; cursor:not-allowed; }

        .flash-ok  { margin:1rem 2rem 0; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:600; }
        .flash-err { margin:1rem 2rem 0; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:600; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('app.call-centar') }}" class="back-btn">← Lista</a>
            <span class="topbar-title">
                #{{ $order->order_number ?? $order->woo_order_id }}
            </span>
            @php
                $statusBadge = match(strtolower($order->status)) {
                    'completed' => 'badge-green',
                    'processing','on-hold' => 'badge-amber',
                    'cancelled','refunded' => 'badge-red',
                    default => 'badge-gray',
                };
                $canComplete = !in_array($order->status, ['cancelled', 'completed']);
                $canCancel   = !in_array($order->status, ['cancelled', 'completed']);
            @endphp
            <span class="badge {{ $statusBadge }}">{{ strtoupper($order->status) }}</span>
        </div>
        <span class="topbar-user">{{ Auth::user()->name }}</span>
    </div>

    @if (session('status'))
        <div class="flash-ok">✓ {{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash-err">✗ {{ $errors->first() }}</div>
    @endif

    <div class="content">
        <div class="layout">

            {{-- LEFT: info --}}
            <div>
                {{-- Customer --}}
                <div class="card">
                    <div class="card-label">Kupac</div>
                    <div class="customer-name">{{ $order->first_name }} {{ $order->last_name }}</div>
                    @if ($order->phone)
                        <div class="customer-phone">📞 {{ $order->phone }}</div>
                    @endif
                    <div class="customer-detail">{{ $order->email ?? '—' }}</div>
                    <div class="customer-detail">{{ $order->address ?? '—' }}, {{ $order->city ?? '—' }} {{ $order->postcode ?? '' }}</div>
                    @if (!empty($order->termal_code))
                        <div class="customer-detail" style="margin-top:0.4rem;">Termal: <strong>{{ $order->termal_code }}</strong></div>
                    @endif
                </div>

                {{-- Total --}}
                <div class="card" style="margin-top:1rem; display:flex; align-items:center; justify-content:space-between;">
                    <div class="total-label">Ukupno</div>
                    <div class="total-amount">{{ number_format((float)$order->total, 2, ',', '.') }} <span style="font-size:1.1rem; font-weight:600; color:#94a3b8;">{{ $order->currency }}</span></div>
                </div>

                {{-- Office notice --}}
                @if (!empty($order->office_notice))
                    <div class="office-notice">
                        <div class="notice-label">⚠ Napomena kancelarije</div>
                        <div class="notice-text">{{ $order->office_notice }}</div>
                    </div>
                @endif

                {{-- Items --}}
                <div class="card" style="margin-top:1rem;">
                    <div class="card-label">Stavke</div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Naziv</th>
                                <th>SKU</th>
                                <th style="text-align:right;">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($order->items as $it)
                                <tr>
                                    <td>
                                        <div class="item-name">{{ $it->name }}</div>
                                        @if ($it->ean) <div class="item-sku">EAN: {{ $it->ean }}</div> @endif
                                    </td>
                                    <td>{{ $it->sku ?? '—' }}</td>
                                    <td style="text-align:right; font-weight:800; color:#1e293b;">{{ $it->qty }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="color:#94a3b8; font-size:0.85rem;">Nema stavki.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RIGHT: actions --}}
            <div>
                {{-- Pickup complete --}}
                <div class="action-card">
                    <div class="action-header">✓ Pickup complete</div>
                    <div class="action-body">
                        <form method="POST" action="{{ route('app.call-centar.complete', $order) }}">
                            @csrf
                            <textarea class="action-textarea" name="note" rows="3" placeholder="Napomena (opciono)…">{{ old('note') }}</textarea>
                            <button
                                type="submit"
                                class="action-btn btn-complete {{ !$canComplete ? 'btn-disabled' : '' }}"
                                {{ !$canComplete ? 'disabled' : '' }}
                                onclick="{{ $canComplete ? "return confirm('Označiti kao completed?')" : 'return false' }}"
                            >Potvrdi preuzimanje</button>
                        </form>
                    </div>
                </div>

                {{-- Cancel --}}
                <div class="action-card">
                    <div class="action-header">✕ Otkaži porudžbinu</div>
                    <div class="action-body">
                        <form method="POST" action="{{ route('app.call-centar.cancel', $order) }}">
                            @csrf
                            <textarea class="action-textarea" name="reason" rows="3" placeholder="Razlog (opciono)…">{{ old('reason') }}</textarea>
                            <button
                                type="submit"
                                class="action-btn btn-cancel {{ !$canCancel ? 'btn-disabled' : '' }}"
                                {{ !$canCancel ? 'disabled' : '' }}
                                onclick="{{ $canCancel ? "return confirm('Otkazati porudžbinu?')" : 'return false' }}"
                            >Otkaži porudžbinu</button>
                        </form>
                    </div>
                </div>

                <div style="padding:0.75rem 0; font-size:0.75rem; color:#cbd5e1; text-align:center;">
                    Office note se menja isključivo iz WooCommerce admina.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
