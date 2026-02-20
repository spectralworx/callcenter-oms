<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pošalji porudžbinu – OMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { font-family:'Figtree',sans-serif; background:#f8fafc; min-height:100vh; display:flex; flex-direction:column; }

        .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 2rem; height:58px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
        .topbar-left { display:flex; align-items:center; gap:1rem; }
        .back-btn { display:inline-flex; align-items:center; text-decoration:none; font-size:0.85rem; font-weight:700; color:#64748b; padding:0.4rem 0.75rem; border:1px solid #e2e8f0; border-radius:8px; transition:all 0.1s; }
        .back-btn:hover { background:#f1f5f9; color:#334155; }
        .topbar-title { font-weight:800; font-size:1.05rem; color:#1e293b; }
        .topbar-user  { font-size:0.85rem; color:#64748b; font-weight:600; }

        .content { flex:1; overflow-y:auto; padding:1.25rem 2rem; display:grid; grid-template-columns:380px 1fr; gap:1.25rem; align-items:start; }

        /* FORM CARD */
        .card { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; overflow:hidden; }
        .card-header { padding:0.9rem 1.25rem; border-bottom:1px solid #f1f5f9; }
        .card-title { font-size:0.9rem; font-weight:800; color:#1e293b; }
        .card-sub { font-size:0.75rem; color:#94a3b8; font-weight:500; margin-top:0.1rem; }
        .card-body { padding:1.25rem; }

        .field { margin-bottom:1rem; }
        .field:last-child { margin-bottom:0; }
        .field-label { font-size:0.75rem; font-weight:700; color:#64748b; display:block; margin-bottom:0.3rem; }
        .field-input { width:100%; border:1.5px solid #e2e8f0; border-radius:8px; padding:0.6rem 0.85rem; font-size:0.9rem; font-family:inherit; font-weight:600; color:#1e293b; outline:none; transition:border-color 0.12s; background:#fff; }
        .field-input:focus { border-color:#818cf8; }
        .field-input::placeholder { color:#94a3b8; font-weight:400; }
        .field-input.textarea { resize:none; }
        .field-hint { font-size:0.72rem; color:#94a3b8; font-weight:500; margin-top:0.25rem; }
        .field-error { font-size:0.75rem; color:#dc2626; font-weight:600; margin-top:0.25rem; }

        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1rem; }

        .btn { display:inline-flex; align-items:center; justify-content:center; padding:0.6rem 1.25rem; border-radius:8px; font-size:0.88rem; font-weight:700; font-family:inherit; cursor:pointer; border:none; transition:all 0.1s; text-decoration:none; white-space:nowrap; }
        .btn:active { transform:scale(0.97); }
        .btn-primary { background:#4f46e5; color:#fff; }
        .btn-primary:hover { background:#4338ca; }
        .btn-ghost { background:#f1f5f9; color:#475569; border:1.5px solid #e2e8f0; }
        .btn-ghost:hover { background:#e2e8f0; }
        .btn-row { display:flex; gap:0.6rem; margin-top:1.25rem; }

        /* HISTORY TABLE */
        table { width:100%; border-collapse:collapse; }
        thead th { padding:0.6rem 1rem; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; text-align:left; background:#f8fafc; border-bottom:1px solid #f1f5f9; }
        thead th.right { text-align:right; }
        tbody td { padding:0.7rem 1rem; border-bottom:1px solid #f8fafc; font-size:0.85rem; color:#334155; font-weight:600; vertical-align:middle; }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover td { background:#fafafa; }
        .td-num { font-weight:800; color:#4338ca; text-decoration:none; }
        .td-num:hover { color:#312e81; }
        .td-right { text-align:right; color:#94a3b8; font-size:0.78rem; }

        .empty-state { padding:2.5rem; text-align:center; }
        .empty-icon { font-size:2rem; color:#e2e8f0; }
        .empty-text { font-size:0.85rem; font-weight:700; color:#94a3b8; margin-top:0.4rem; }

        .flash-ok  { margin:1rem 2rem 0; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.88rem; font-weight:600; flex-shrink:0; }
    </style>
</head>
<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('app.home') }}" class="back-btn">← Nazad</a>
            <span class="topbar-title">✉️ Pošalji porudžbinu</span>
        </div>
        <span class="topbar-user">{{ Auth::user()->name }}</span>
    </div>

    @if (session('status'))
        <div class="flash-ok">✓ {{ session('status') }}</div>
    @endif

    <div class="content">

        {{-- FORM --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Resend email</div>
                <div class="card-sub">Unesi broj porudžbine ili email kupca</div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ url()->current() }}">
                    @csrf

                    <div class="field">
                        <label class="field-label">Porudžbina / Email</label>
                        <input
                            class="field-input"
                            name="q"
                            value="{{ old('q', request('order_id') ? ('#'.request('order_id')) : '') }}"
                            placeholder="#12345 ili email@kupac.com"
                            autofocus
                        />
                        @error('q') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field-row">
                        <div>
                            <label class="field-label">Tip poruke</label>
                            @php $tpl = old('template', 'order_confirmation'); @endphp
                            <select class="field-input" name="template">
                                <option value="order_confirmation" @selected($tpl==='order_confirmation')>Potvrda porudžbine</option>
                                <option value="payment_link"       @selected($tpl==='payment_link')>Link za plaćanje</option>
                                <option value="shipping_update"    @selected($tpl==='shipping_update')>Update isporuke</option>
                                <option value="custom"             @selected($tpl==='custom')>Custom</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label">Primaoc</label>
                            @php $rcp = old('recipient', 'customer'); @endphp
                            <select class="field-input" name="recipient">
                                <option value="customer" @selected($rcp==='customer')>Kupac</option>
                                <option value="billing"  @selected($rcp==='billing')>Billing email</option>
                                <option value="override" @selected($rcp==='override')>Ručni email</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Ručni email (opciono)</label>
                        <input class="field-input" type="email" name="override_email" value="{{ old('override_email') }}" placeholder="Samo ako je primaoc = Ručni email" />
                        @error('override_email') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="field">
                        <label class="field-label">Napomena (interno / audit log)</label>
                        <textarea class="field-input textarea" name="note" rows="3" placeholder="Zašto šaljemo?…">{{ old('note') }}</textarea>
                        @error('note') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="btn-row">
                        <button type="submit" class="btn btn-primary">✉️ Pošalji</button>
                        <a href="{{ route('app.call-centar') }}" class="btn btn-ghost">Otvori listu</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- HISTORY --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">Istorija slanja</div>
                <div class="card-sub">Poslednje resend akcije</div>
            </div>

            @php $history = $history ?? collect(); @endphp

            <table>
                <thead>
                    <tr>
                        <th>Porudžbina</th>
                        <th>Template</th>
                        <th>Primaoc</th>
                        <th>Korisnik</th>
                        <th class="right">Vreme</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $h)
                        @php
                            $number = $h->order_number ?? $h['order_number'] ?? ($h->order_id ?? $h['order_id'] ?? '—');
                            $tpl2   = $h->template     ?? $h['template']     ?? '—';
                            $to     = $h->to           ?? $h['to']           ?? '—';
                            $by     = $h->user_name    ?? $h['user_name']    ?? '—';
                            $when   = $h->created_at   ?? $h['created_at']   ?? null;
                        @endphp
                        <tr>
                            <td>
                                <a class="td-num" href="{{ route('app.call-centar.show', $h->order_id ?? $h['order_id'] ?? 0) }}">
                                    #{{ $number }}
                                </a>
                            </td>
                            <td>
                                <span style="background:#ede9fe; color:#5b21b6; padding:0.15rem 0.55rem; border-radius:99px; font-size:0.72rem; font-weight:700;">
                                    {{ $tpl2 }}
                                </span>
                            </td>
                            <td style="font-size:0.82rem;">{{ $to }}</td>
                            <td style="font-size:0.82rem; color:#64748b;">{{ $by }}</td>
                            <td class="td-right">
                                {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <div class="empty-icon">✉️</div>
                                    <div class="empty-text">Nema istorije slanja</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>
