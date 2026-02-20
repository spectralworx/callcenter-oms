<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Call Centar – OMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Figtree',sans-serif; background:#f8fafc; min-height:100vh; display:flex; flex-direction:column; }

        /* TOPBAR */
        .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 2rem; height:58px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
        .topbar-left { display:flex; align-items:center; gap:1rem; }
        .back-btn { display:inline-flex; align-items:center; gap:0.4rem; text-decoration:none; font-size:0.85rem; font-weight:700; color:#64748b; padding:0.4rem 0.75rem; border:1px solid #e2e8f0; border-radius:8px; transition:all 0.1s; }
        .back-btn:hover { background:#f1f5f9; color:#334155; }
        .topbar-title { font-weight:800; font-size:1.05rem; color:#1e293b; }
        .topbar-user { font-size:0.85rem; color:#64748b; font-weight:600; }

        /* SEARCH */
        .search-bar { background:#fff; border-bottom:1px solid #e2e8f0; padding:1rem 2rem; display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap; flex-shrink:0; }
        .search-bar input, .search-bar select {
            border:1.5px solid #e2e8f0; border-radius:10px; padding:0.6rem 1rem; font-size:0.95rem; font-family:inherit;
            font-weight:600; color:#1e293b; background:#fff; outline:none; transition:border-color 0.12s;
        }
        .search-bar input { flex:1; min-width:240px; }
        .search-bar input::placeholder { color:#94a3b8; font-weight:400; }
        .search-bar input:focus, .search-bar select:focus { border-color:#818cf8; }
        .search-bar select { min-width:160px; cursor:pointer; }
        .btn { display:inline-flex; align-items:center; padding:0.6rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:700; font-family:inherit; cursor:pointer; border:none; transition:all 0.1s; text-decoration:none; white-space:nowrap; }
        .btn-primary { background:#4f46e5; color:#fff; }
        .btn-primary:hover { background:#4338ca; }
        .btn-ghost { background:#f1f5f9; color:#475569; border:1.5px solid #e2e8f0; }
        .btn-ghost:hover { background:#e2e8f0; }

        /* LIST */
        .list { flex:1; overflow-y:auto; padding:1.25rem 2rem; display:flex; flex-direction:column; gap:0.5rem; }

        .order-row {
            background:#fff; border:1.5px solid #e2e8f0; border-radius:12px; padding:1rem 1.25rem;
            display:flex; align-items:center; gap:1rem; text-decoration:none; transition:all 0.1s;
        }
        .order-row:hover { border-color:#a5b4fc; box-shadow:0 2px 12px rgba(79,70,229,0.07); transform:translateY(-1px); }
        .order-row:active { transform:scale(0.995); }

        .order-num { font-size:1.1rem; font-weight:800; color:#1e293b; min-width:90px; }
        .order-name { font-size:0.95rem; font-weight:700; color:#334155; flex:1; }
        .order-meta { font-size:0.8rem; color:#94a3b8; font-weight:500; }
        .order-total { font-size:1rem; font-weight:800; color:#1e293b; min-width:90px; text-align:right; }
        .order-date { font-size:0.78rem; color:#94a3b8; font-weight:500; text-align:right; }

        .badge { display:inline-flex; align-items:center; padding:0.2rem 0.6rem; border-radius:99px; font-size:0.72rem; font-weight:700; letter-spacing:0.02em; }
        .badge-gray    { background:#f1f5f9; color:#475569; }
        .badge-green   { background:#dcfce7; color:#15803d; }
        .badge-amber   { background:#fef3c7; color:#b45309; }
        .badge-red     { background:#fee2e2; color:#dc2626; }

        .arrow { color:#cbd5e1; font-size:1.2rem; font-weight:800; flex-shrink:0; }

        .empty-state { flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#cbd5e1; gap:0.5rem; padding:3rem; }
        .empty-state .icon { font-size:3rem; }
        .empty-state .text { font-size:1rem; font-weight:700; color:#94a3b8; }

        .flash-ok  { margin:1rem 2rem 0; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:600; }
        .flash-err { margin:1rem 2rem 0; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:600; }

        .pagination-wrap { padding:0.75rem 2rem; border-top:1px solid #e2e8f0; background:#fff; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('app.home') }}" class="back-btn">← Nazad</a>
            <span class="topbar-title">🔍 Call Centar</span>
        </div>
        <span class="topbar-user">{{ Auth::user()->name }}</span>
    </div>

    <div class="search-bar">
        <form method="GET" action="{{ route('app.call-centar') }}" style="display:contents;">
            <input name="q" value="{{ $filters['q'] }}" placeholder="Ime, telefon, email, #broj…" />
            <select name="status">
                <option value="">Svi statusi</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st }}" @selected($filters['status'] === $st)>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Pretraži</button>
            @if ($filters['q'] || $filters['status'])
                <a href="{{ route('app.call-centar') }}" class="btn btn-ghost">✕ Reset</a>
            @endif
        </form>
    </div>

    @if (session('status'))
        <div class="flash-ok">✓ {{ session('status') }}</div>
    @endif

    <div class="list">
        @forelse ($orders as $o)
            @php
                $statusBadge = match(strtolower($o->status)) {
                    'completed' => 'badge-green',
                    'processing', 'on-hold' => 'badge-amber',
                    'cancelled', 'refunded' => 'badge-red',
                    default => 'badge-gray',
                };
            @endphp
            <a href="{{ route('app.call-centar.show', $o) }}" class="order-row">
                <div>
                    <div class="order-num">#{{ $o->order_number ?? $o->woo_order_id }}</div>
                    <span class="badge {{ $statusBadge }}">{{ strtoupper($o->status) }}</span>
                    @if ($o->office_notice)
                        <span class="badge badge-amber" style="margin-left:4px;">⚠ Note</span>
                    @endif
                </div>
                <div style="flex:1; min-width:0;">
                    <div class="order-name">{{ $o->first_name }} {{ $o->last_name }}</div>
                    <div class="order-meta">{{ $o->phone ?? '—' }} · {{ $o->city ?? '—' }}</div>
                </div>
                <div style="text-align:right; flex-shrink:0;">
                    <div class="order-total">{{ number_format((float)$o->total, 0, ',', '.') }} {{ $o->currency }}</div>
                    <div class="order-date">{{ $o->created_at?->format('d.m.Y') }}</div>
                </div>
                <div class="arrow">›</div>
            </a>
        @empty
            <div class="empty-state">
                <div class="icon">🔍</div>
                <div class="text">Nema rezultata za zadate filtere</div>
            </div>
        @endforelse
    </div>

    @if ($orders->hasPages())
        <div class="pagination-wrap">{{ $orders->links() }}</div>
    @endif
</body>
</html>
