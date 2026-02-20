<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pošalji porudžbinu – OMS</title>
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
        .back-btn { display:inline-flex; align-items:center; text-decoration:none; font-size:0.85rem; font-weight:700; color:#64748b; padding:0.4rem 0.75rem; border:1px solid #e2e8f0; border-radius:8px; transition:all 0.1s; }
        .back-btn:hover { background:#f1f5f9; color:#334155; }
        .topbar-title { font-weight:800; font-size:1.05rem; color:#1e293b; }
        .topbar-user  { font-size:0.85rem; color:#64748b; font-weight:600; }

        .flash-ok  { margin:1rem 2rem 0; background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.88rem; font-weight:600; }
        .flash-err { margin:1rem 2rem 0; background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:0.75rem 1.25rem; border-radius:10px; font-size:0.88rem; font-weight:600; }

        .content { flex:1; padding:1.5rem 2rem; display:grid; grid-template-columns:480px 1fr; gap:1.25rem; align-items:start; }

        .card { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; overflow:hidden; }

        /* COMPOSE */
        .compose-top { padding:1.25rem 1.5rem 0; }
        .compose-title { font-size:1.15rem; font-weight:900; color:#1e293b; letter-spacing:-0.02em; }
        .compose-sub { font-size:0.78rem; color:#94a3b8; font-weight:500; margin-top:0.15rem; }
        .compose-body { padding:1.25rem 1.5rem 1.5rem; }
        .hr { height:1px; background:#f1f5f9; margin:1.1rem 0; }

        .field { margin-bottom:0.85rem; }
        .field-label { font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; display:block; margin-bottom:0.3rem; }
        .field-input { width:100%; border:1.5px solid #e2e8f0; border-radius:8px; padding:0.65rem 0.9rem; font-size:0.95rem; font-family:inherit; font-weight:600; color:#1e293b; outline:none; transition:border-color 0.12s; background:#fff; }
        .field-input:focus { border-color:#818cf8; box-shadow:0 0 0 3px rgba(129,140,248,0.08); }
        .field-input::placeholder { color:#cbd5e1; font-weight:400; }
        .field-error { font-size:0.75rem; color:#dc2626; font-weight:600; margin-top:0.2rem; }

        /* ITEMS */
        .items-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.55rem; }
        .items-header-label { font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.06em; }
        .items-count { font-size:0.72rem; font-weight:600; color:#cbd5e1; }

        .items-list { border:1.5px solid #e2e8f0; border-radius:10px; overflow:hidden; margin-bottom:0.6rem; }
        .item-row { display:grid; grid-template-columns:1fr 72px 30px; gap:0.5rem; padding:0.55rem 0.75rem; border-bottom:1px solid #f8fafc; align-items:center; }
        .item-row:last-child { border-bottom:none; }

        .item-name { border:none; outline:none; font-family:inherit; background:transparent; font-size:0.9rem; font-weight:600; color:#1e293b; width:100%; }
        .item-name::placeholder { color:#cbd5e1; font-weight:400; font-size:0.88rem; }
        .item-name:focus { background:#fafbff; border-radius:4px; padding-left:4px; margin-left:-4px; }

        .item-qty { border:1.5px solid #e2e8f0; border-radius:6px; padding:0.25rem 0.4rem; font-family:inherit; font-size:0.88rem; font-weight:700; color:#475569; text-align:center; outline:none; background:#fff; width:100%; }
        .item-qty:focus { border-color:#818cf8; background:#fafbff; }

        .item-remove { display:flex; align-items:center; justify-content:center; width:28px; height:28px; border:none; background:none; cursor:pointer; color:#dde1e7; font-size:0.95rem; border-radius:6px; transition:all 0.1s; flex-shrink:0; }
        .item-remove:hover { background:#fef2f2; color:#ef4444; }

        .add-item-btn { display:flex; align-items:center; justify-content:center; gap:0.4rem; width:100%; background:none; border:1.5px dashed #e2e8f0; border-radius:8px; padding:0.55rem; font-size:0.82rem; font-weight:700; color:#94a3b8; cursor:pointer; font-family:inherit; transition:all 0.1s; margin-bottom:0.85rem; }
        .add-item-btn:hover { border-color:#818cf8; color:#4f46e5; background:#fafbff; }

        .send-btn { display:flex; align-items:center; justify-content:center; gap:0.5rem; width:100%; padding:0.9rem; background:#4f46e5; color:#fff; border:none; border-radius:10px; font-size:1rem; font-weight:800; font-family:inherit; cursor:pointer; transition:all 0.1s; letter-spacing:-0.01em; }
        .send-btn:hover { background:#4338ca; }
        .send-btn:active { transform:scale(0.98); }

        /* HISTORY */
        .hist-header { padding:0.9rem 1.25rem; border-bottom:1px solid #f1f5f9; }
        .hist-title { font-size:0.9rem; font-weight:800; color:#1e293b; }
        .hist-sub { font-size:0.75rem; color:#94a3b8; margin-top:0.1rem; }

        table { width:100%; border-collapse:collapse; }
        thead th { padding:0.6rem 1rem; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; text-align:left; background:#f8fafc; border-bottom:1px solid #f1f5f9; }
        tbody td { padding:0.7rem 1rem; border-bottom:1px solid #f8fafc; font-size:0.85rem; color:#334155; font-weight:600; vertical-align:middle; }
        tbody tr:last-child td { border-bottom:none; }
        tbody tr:hover td { background:#fafafa; }

        .empty-state { padding:2.5rem; text-align:center; }
        .empty-icon { font-size:2rem; }
        .empty-text { font-size:0.85rem; font-weight:700; color:#94a3b8; margin-top:0.5rem; }
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
    @if ($errors->any())
        <div class="flash-err">✗ {{ $errors->first() }}</div>
    @endif

    <div class="content">

        {{-- ── COMPOSE ──────────────────────────────── --}}
        <div class="card">
            <div class="compose-top">
                <div class="compose-title">Nova poruka</div>
                <div class="compose-sub">Manuelno slanje putem Resend servisa</div>
            </div>

            <div class="compose-body">
                <form method="POST" action="{{ url()->current() }}">
                    @csrf

                    <div class="hr" style="margin-top:1rem;"></div>

                    {{-- PRIMA --}}
                    <div class="field">
                        <label class="field-label">Prima</label>
                        <input
                            class="field-input"
                            type="email"
                            name="to"
                            value="{{ old('to') }}"
                            placeholder="kupac@email.com"
                            autofocus
                            required
                        />
                        @error('to') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    {{-- SUBJECT --}}
                    <div class="field">
                        <label class="field-label">Subject</label>
                        <input
                            class="field-input"
                            type="text"
                            name="subject"
                            value="{{ old('subject', 'Vaša porudžbina je potvrđena') }}"
                            placeholder="Naslov emaila…"
                            required
                        />
                        @error('subject') <div class="field-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="hr"></div>

                    {{-- STAVKE --}}
                    <div class="items-header">
                        <span class="items-header-label">Stavke porudžbine</span>
                        <span class="items-count" id="itemCount">—</span>
                    </div>

                    <div class="items-list" id="itemsList">
                        @php $oldItems = old('items', [['name'=>'','qty'=>1]]); @endphp
                        @foreach ($oldItems as $i => $it)
                            <div class="item-row">
                                <input
                                    type="text"
                                    name="items[{{ $i }}][name]"
                                    value="{{ $it['name'] ?? '' }}"
                                    placeholder="Naziv proizvoda…"
                                    class="item-name"
                                />
                                <input
                                    type="number"
                                    name="items[{{ $i }}][qty]"
                                    value="{{ $it['qty'] ?? 1 }}"
                                    min="1"
                                    class="item-qty"
                                    title="Količina"
                                />
                                <button type="button" class="item-remove" onclick="removeItem(this)">✕</button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="add-item-btn" onclick="addItem()">+ Dodaj stavku</button>

                    <div class="hr"></div>

                    {{-- NAPOMENA --}}
                    <div class="field" style="margin-bottom:1.25rem;">
                        <label class="field-label">Interna napomena (neće biti u emailu)</label>
                        <input
                            class="field-input"
                            type="text"
                            name="note"
                            value="{{ old('note') }}"
                            placeholder="Za audit log…"
                        />
                    </div>

                    <button type="submit" class="send-btn">✉️ Pošalji email</button>

                </form>
            </div>
        </div>

        {{-- ── ISTORIJA ─────────────────────────────── --}}
        <div class="card">
            <div class="hist-header">
                <div class="hist-title">Istorija slanja</div>
                <div class="hist-sub">Poruke poslate putem Resend-a</div>
            </div>

            @php $history = $history ?? collect(); @endphp

            <table>
                <thead>
                    <tr>
                        <th>Prima</th>
                        <th>Subject</th>
                        <th>Korisnik</th>
                        <th style="text-align:right;">Vreme</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $h)
                        @php
                            $to   = $h->to          ?? $h['to']         ?? '—';
                            $subj = $h->subject      ?? $h['subject']    ?? '—';
                            $by   = $h->user_name    ?? $h['user_name']  ?? '—';
                            $when = $h->created_at   ?? $h['created_at'] ?? null;
                        @endphp
                        <tr>
                            <td style="font-size:0.82rem;">{{ $to }}</td>
                            <td style="font-size:0.82rem; color:#475569; max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $subj }}</td>
                            <td style="font-size:0.78rem; color:#94a3b8;">{{ $by }}</td>
                            <td style="text-align:right; font-size:0.78rem; color:#94a3b8;">
                                {{ $when ? \Illuminate\Support\Carbon::parse($when)->format('d.m.Y H:i') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-icon">✉️</div>
                                    <div class="empty-text">Još nema poslatih poruka</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <script>
        let idx = {{ count(old('items', [['name'=>'','qty'=>1]])) }};

        function updateCount() {
            const n = document.querySelectorAll('#itemsList .item-row').length;
            document.getElementById('itemCount').textContent =
                n === 0 ? '—' : n + (n === 1 ? ' stavka' : n < 5 ? ' stavke' : ' stavki');
        }

        function addItem() {
            const list = document.getElementById('itemsList');
            const row = document.createElement('div');
            row.className = 'item-row';
            row.innerHTML = `
                <input type="text" name="items[${idx}][name]" placeholder="Naziv proizvoda…" class="item-name" />
                <input type="number" name="items[${idx}][qty]" value="1" min="1" class="item-qty" title="Količina" />
                <button type="button" class="item-remove" onclick="removeItem(this)">✕</button>
            `;
            list.appendChild(row);
            idx++;
            updateCount();
            row.querySelector('input').focus();
        }

        function removeItem(btn) {
            const list = document.getElementById('itemsList');
            const row = btn.closest('.item-row');
            if (list.children.length > 1) {
                row.remove();
            } else {
                row.querySelector('.item-name').value = '';
                row.querySelector('.item-qty').value = 1;
            }
            updateCount();
        }

        document.addEventListener('DOMContentLoaded', updateCount);
    </script>

</body>
</html>
