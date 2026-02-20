<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lično preuzimanje – OMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Figtree',sans-serif; background:#f8fafc; min-height:100vh; display:flex; flex-direction:column; }

        .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 2rem; height:58px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
        .topbar-left { display:flex; align-items:center; gap:1rem; }
        .back-btn { display:inline-flex; align-items:center; gap:0.4rem; text-decoration:none; font-size:0.85rem; font-weight:700; color:#64748b; padding:0.4rem 0.75rem; border:1px solid #e2e8f0; border-radius:8px; transition:all 0.1s; }
        .back-btn:hover { background:#f1f5f9; color:#334155; }
        .topbar-title { font-weight:800; font-size:1.05rem; color:#1e293b; }
        .topbar-user { font-size:0.85rem; color:#64748b; font-weight:600; }

        .layout { flex:1; display:grid; grid-template-columns:420px 1fr; gap:1.25rem; padding:1.5rem 2rem; }

        .card { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; }

        /* SCAN PANEL */
        .scan-body { padding:1.5rem; }
        .scan-title { font-size:1.1rem; font-weight:800; color:#1e293b; margin-bottom:0.25rem; }
        .scan-sub { font-size:0.82rem; color:#94a3b8; font-weight:500; margin-bottom:1.25rem; }

        .scan-input { width:100%; border:2px solid #e2e8f0; border-radius:12px; padding:0.9rem 1.1rem; font-size:1.2rem; font-family:inherit; font-weight:700; color:#1e293b; letter-spacing:0.04em; outline:none; transition:border-color 0.12s; }
        .scan-input:focus { border-color:#4f46e5; }
        .scan-input::placeholder { color:#cbd5e1; font-weight:400; letter-spacing:0; font-size:1rem; }

        .scan-actions { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-top:0.9rem; }
        .btn { display:flex; align-items:center; justify-content:center; padding:0.85rem; border-radius:10px; font-size:0.95rem; font-weight:800; font-family:inherit; cursor:pointer; border:none; transition:all 0.1s; }
        .btn:active { transform:scale(0.97); }
        .btn-confirm { background:#059669; color:#fff; }
        .btn-confirm:hover { background:#047857; }
        .btn-clear { background:#f1f5f9; color:#475569; border:1.5px solid #e2e8f0; }
        .btn-clear:hover { background:#e2e8f0; }

        .scan-tip { margin-top:1rem; background:#f8fafc; border-radius:10px; padding:0.75rem 1rem; font-size:0.78rem; color:#94a3b8; font-weight:500; line-height:1.5; }

        /* RECENT LIST */
        .recent-header { padding:0.9rem 1.25rem; border-bottom:1px solid #f1f5f9; font-size:0.9rem; font-weight:800; color:#1e293b; }
        .recent-list { overflow-y:auto; max-height:calc(100vh - 160px); }
        .recent-row { display:flex; align-items:center; justify-content:space-between; padding:0.85rem 1.25rem; border-bottom:1px solid #f8fafc; gap:1rem; }
        .recent-row:last-child { border-bottom:none; }
        .recent-num { font-size:1rem; font-weight:800; color:#1e293b; }
        .recent-name { font-size:0.82rem; color:#64748b; font-weight:500; }
        .recent-time { font-size:0.8rem; color:#94a3b8; font-weight:600; text-align:right; }
        .badge-done { background:#dcfce7; color:#15803d; font-size:0.7rem; font-weight:700; padding:0.15rem 0.55rem; border-radius:99px; }

        .empty-state { padding:3rem 1.25rem; text-align:center; color:#cbd5e1; }
        .empty-state .icon { font-size:2.5rem; }
        .empty-state .text { font-size:0.9rem; font-weight:600; margin-top:0.5rem; color:#94a3b8; }

        .flash-ok  { margin:1rem 2rem 0; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:700; }
        .flash-err { margin:1rem 2rem 0; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.9rem; font-weight:700; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('app.home') }}" class="back-btn">← Nazad</a>
            <span class="topbar-title">📦 Lično preuzimanje</span>
        </div>
        <span class="topbar-user">{{ Auth::user()->name }}</span>
    </div>

    @if (session('status'))
        <div class="flash-ok">✓ {{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="flash-err">✗ {{ $errors->first() }}</div>
    @endif

    <div class="layout">
        {{-- Scan panel --}}
        <div class="card">
            <div class="scan-body">
                <div class="scan-title">Skeniraj ili upiši kod</div>
                <div class="scan-sub">Termal broj ili QR kod</div>

                <form method="POST" action="{{ url()->current() }}" id="scanForm">
                    @csrf
                    <input
                        type="text"
                        name="code"
                        id="codeInput"
                        value="{{ old('code') }}"
                        class="scan-input"
                        placeholder="npr. #12345 ili barkod…"
                        autofocus
                        autocomplete="off"
                    />
                    <div class="scan-actions">
                        <button type="submit" class="btn btn-confirm">✓ Potvrdi</button>
                        <button type="button" class="btn btn-clear"
                            onclick="document.getElementById('codeInput').value=''; document.getElementById('codeInput').focus();">
                            ✕ Briši
                        </button>
                    </div>
                </form>

                <div class="scan-tip">
                    💡 Scanner automatski šalje Enter — forma se odmah predaje. Posle potvrde fokus se vraća na polje za sledeći sken.
                </div>
            </div>
        </div>

        {{-- Recent --}}
        <div class="card" style="display:flex; flex-direction:column;">
            <div class="recent-header">Poslednja preuzimanja</div>

            @php $recent = $recent ?? collect(); @endphp

            <div class="recent-list">
                @forelse ($recent as $r)
                    @php
                        $number = $r->number ?? $r['number'] ?? ($r->id ?? $r['id'] ?? '—');
                        $cust   = $r->customer_name ?? $r['customer_name'] ?? '—';
                        $when   = $r->picked_up_at ?? $r['picked_up_at'] ?? ($r->updated_at ?? $r['updated_at'] ?? null);
                    @endphp
                    <div class="recent-row">
                        <div>
                            <div class="recent-num">#{{ $number }}</div>
                            <div class="recent-name">{{ $cust }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div><span class="badge-done">COMPLETED</span></div>
                            <div class="recent-time">{{ $when ? \Illuminate\Support\Carbon::parse($when)->format('H:i') : '—' }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="icon">📭</div>
                        <div class="text">Još nema preuzimanja danas</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('codeInput');
            if (input) input.focus();
        });
    </script>
</body>
</html>
