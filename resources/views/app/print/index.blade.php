<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Štampa – OMS</title>
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
        .topbar-user  { font-size:0.85rem; color:#64748b; font-weight:600; }

        /* TABS + SEARCH */
        .toolbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0.75rem 2rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; flex-shrink:0; }
        .tabs { display:flex; gap:0.4rem; }
        .tab-link { padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:700; text-decoration:none; transition:all 0.1s; border:1.5px solid transparent; }
        .tab-link.active { background:#4f46e5; color:#fff; border-color:#4f46e5; }
        .tab-link:not(.active) { background:#f1f5f9; color:#475569; }
        .tab-link:not(.active):hover { background:#e2e8f0; }

        .search-form { display:flex; gap:0.6rem; align-items:center; }
        .search-form input { border:1.5px solid #e2e8f0; border-radius:8px; padding:0.45rem 0.85rem; font-size:0.85rem; font-family:inherit; font-weight:600; color:#1e293b; outline:none; width:220px; transition:border-color 0.12s; }
        .search-form input:focus { border-color:#818cf8; }
        .search-form input::placeholder { color:#94a3b8; font-weight:400; }
        .btn { display:inline-flex; align-items:center; justify-content:center; padding:0.45rem 1rem; border-radius:8px; font-size:0.85rem; font-weight:700; font-family:inherit; cursor:pointer; border:none; transition:all 0.1s; text-decoration:none; white-space:nowrap; }
        .btn:active { transform:scale(0.97); }
        .btn-primary { background:#4f46e5; color:#fff; }
        .btn-primary:hover { background:#4338ca; }
        .btn-dark { background:#1e293b; color:#fff; }
        .btn-dark:hover { background:#0f172a; }
        .btn-ghost { background:#f1f5f9; color:#475569; border:1.5px solid #e2e8f0; }
        .btn-ghost:hover { background:#e2e8f0; }

        /* CONTENT */
        .content { flex:1; overflow-y:auto; padding:1.25rem 2rem; }

        .card { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; overflow:hidden; }
        .card-header { padding:0.9rem 1.25rem; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .card-title { font-size:0.9rem; font-weight:800; color:#1e293b; }
        .card-sub { font-size:0.75rem; color:#94a3b8; font-weight:500; }

        /* TABLE */
        table { width:100%; border-collapse:collapse; }
        thead th { padding:0.65rem 1rem; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; text-align:left; background:#f8fafc; border-bottom:1px solid #f1f5f9; }
        thead th.right { text-align:right; }
        tbody td { padding:0.75rem 1rem; border-bottom:1px solid #f8fafc; font-size:0.88rem; color:#334155; font-weight:600; vertical-align:middle; }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover td { background:#fafafa; }
        .td-num { font-weight:800; color:#1e293b; }
        .td-sub { font-size:0.72rem; color:#94a3b8; font-weight:500; }
        .td-right { text-align:right; }

        .chk { width:1.1rem; height:1.1rem; accent-color:#4f46e5; cursor:pointer; }

        /* BULK */
        .bulk-layout { display:grid; grid-template-columns:360px 1fr; gap:1.25rem; }
        .bulk-panel { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px; padding:1.25rem; }
        .bulk-panel textarea { width:100%; border:1.5px solid #e2e8f0; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.85rem; font-family:'Figtree',monospace; color:#334155; font-weight:500; resize:vertical; outline:none; transition:border-color 0.12s; background:#fff; margin-top:0.75rem; }
        .bulk-panel textarea:focus { border-color:#818cf8; }
        .bulk-panel textarea::placeholder { color:#94a3b8; }
        .bulk-preview { background:#fff; border:1.5px dashed #e2e8f0; border-radius:12px; display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:200px; padding:2rem; text-align:center; color:#cbd5e1; }
        .bulk-preview .preview-icon { font-size:2rem; margin-bottom:0.5rem; }
        .bulk-preview .preview-text { font-size:0.85rem; font-weight:600; color:#94a3b8; }

        .field-label { font-size:0.75rem; font-weight:700; color:#64748b; margin-bottom:0.25rem; }

        .empty-state { padding:3rem; text-align:center; }
        .empty-state .icon { font-size:2.5rem; color:#e2e8f0; }
        .empty-state .text { font-size:0.88rem; font-weight:700; color:#94a3b8; margin-top:0.5rem; }
        .empty-state .sub  { font-size:0.78rem; color:#cbd5e1; margin-top:0.2rem; }

        .flash-ok  { margin:1rem 2rem 0; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.88rem; font-weight:600; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('app.home') }}" class="back-btn">← Nazad</a>
            <span class="topbar-title">🖨️ Štampa</span>
        </div>
        <span class="topbar-user">{{ Auth::user()->name }}</span>
    </div>

    @php
        $tab  = request('tab', 'unprinted');
        $tabs = ['unprinted' => 'Neštampane', 'bulk' => 'Bulk štampa', 'history' => 'Istorija'];
    @endphp

    <div class="toolbar">
        <div class="tabs">
            @foreach ($tabs as $key => $label)
                <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
                   class="tab-link {{ $tab === $key ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <form method="GET" action="{{ url()->current() }}" class="search-form">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input name="q" value="{{ request('q') }}" placeholder="Pretraga (#, ime, email)…" />
            <button type="submit" class="btn btn-primary">Traži</button>
        </form>
    </div>

    @if (session('status'))
        <div class="flash-ok">✓ {{ session('status') }}</div>
    @endif

    <div class="content">

        {{-- ── NEŠTAMPANE ───────────────────────────── --}}
        @if ($tab === 'unprinted')
            @php $unprinted = $unprinted ?? collect(); @endphp

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Neštampane porudžbine</div>
                        <div class="card-sub">Označi više i štampaj odjednom</div>
                    </div>
                    <form method="POST" action="{{ url()->current() }}" id="printSelectedForm">
                        @csrf
                        <input type="hidden" name="action" value="print_selected">
                        <button type="submit" class="btn btn-dark">🖨️ Štampaj označene</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th style="width:48px; padding-left:1.25rem;">
                                <input type="checkbox" id="checkAll" class="chk">
                            </th>
                            <th>Porudžbina</th>
                            <th>Kupac</th>
                            <th>Iznos</th>
                            <th class="right">Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unprinted as $o)
                            @php
                                $id      = $o->id ?? $o['id'] ?? null;
                                $num     = $o->order_number ?? $o->number ?? $o['number'] ?? $id;
                                $cust    = $o->customer_name ?? $o['customer_name'] ?? '—';
                                $total   = $o->total ?? $o['total'] ?? '—';
                                $created = $o->created_at ?? $o['created_at'] ?? null;
                            @endphp
                            <tr>
                                <td style="padding-left:1.25rem;">
                                    <input type="checkbox" name="order_ids[]" value="{{ $id }}" class="chk rowCheck" form="printSelectedForm">
                                </td>
                                <td>
                                    <a class="td-num" href="{{ route('app.call-centar.show', $id) }}" style="text-decoration:none; color:#4338ca;">
                                        #{{ $num }}
                                    </a>
                                    <div class="td-sub">Neštampano</div>
                                </td>
                                <td>{{ $cust }}</td>
                                <td>{{ is_numeric($total) ? number_format((float)$total, 2, ',', '.') : $total }}</td>
                                <td class="td-right" style="color:#94a3b8; font-size:0.8rem;">
                                    {{ $created ? \Illuminate\Support\Carbon::parse($created)->format('d.m.Y H:i') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="icon">🖨️</div>
                                        <div class="text">Nema neštampanih porudžbina</div>
                                        <div class="sub">Kad dođu nove porudžbine, pojaviće se ovde</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const all = document.getElementById('checkAll');
                    const rows = Array.from(document.querySelectorAll('.rowCheck'));
                    if (all) all.addEventListener('change', () => rows.forEach(r => r.checked = all.checked));
                });
            </script>

        {{-- ── BULK ─────────────────────────────────── --}}
        @elseif ($tab === 'bulk')
            <div class="bulk-layout">
                <div class="bulk-panel">
                    <div class="card-title">Bulk štampa</div>
                    <div class="card-sub" style="margin-top:0.2rem;">Nalepi brojeve porudžbina, jedan po liniji</div>
                    <form method="POST" action="{{ url()->current() }}">
                        @csrf
                        <input type="hidden" name="action" value="bulk_print">
                        <textarea name="bulk" rows="12" placeholder="#12345&#10;#12346&#10;#12347">{{ old('bulk') }}</textarea>
                        @error('bulk')
                            <div style="color:#dc2626; font-size:0.78rem; margin-top:0.4rem; font-weight:600;">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn btn-dark" style="margin-top:0.75rem; width:100%;">🖨️ Štampaj</button>
                    </form>
                    <div style="margin-top:0.75rem; font-size:0.75rem; color:#94a3b8; font-weight:500;">
                        Tip: unos bez "#" je isto prihvatljiv.
                    </div>
                </div>

                <div class="bulk-preview">
                    <div class="preview-icon">📋</div>
                    <div class="preview-text">Preview rezultata<br><span style="font-size:0.75rem; color:#cbd5e1;">Pronađene / promašene / već štampane</span></div>
                </div>
            </div>

        {{-- ── ISTORIJA ─────────────────────────────── --}}
        @elseif ($tab === 'history')
            @php $printed = $printed ?? collect(); @endphp

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Istorija štampe</div>
                        <div class="card-sub">Ko je i kada štampao</div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Porudžbina</th>
                            <th>Korisnik</th>
                            <th>Tip</th>
                            <th class="right">Vreme</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($printed as $p)
                            @php
                                $num  = $p->order_number ?? $p['order_number'] ?? '—';
                                $by   = $p->user_name    ?? $p['user_name']    ?? '—';
                                $type = $p->type         ?? $p['type']         ?? 'label';
                                $when = $p->created_at   ?? $p['created_at']   ?? null;
                            @endphp
                            <tr>
                                <td class="td-num">#{{ $num }}</td>
                                <td>{{ $by }}</td>
                                <td>
                                    <span style="background:#f1f5f9; color:#475569; padding:0.15rem 0.55rem; border-radius:99px; font-size:0.72rem; font-weight:700;">
                                        {{ strtoupper($type) }}
                                    </span>
                                </td>
                                <td class="td-right" style="color:#94a3b8; font-size:0.8rem;">
                                    {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <div class="icon">📋</div>
                                        <div class="text">Nema istorije štampe</div>
                                        <div class="sub">Kad krene štampa, ovde će se beležiti</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

    </div>
</body>
</html>
