<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Call Center OMS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Figtree', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }

        .topbar { background:#fff; border-bottom:1px solid #e2e8f0; padding:0 2rem; height:58px; display:flex; align-items:center; justify-content:space-between; }
        .topbar-brand { font-weight:800; font-size:1.05rem; color:#1e293b; letter-spacing:-0.01em; }
        .topbar-user { font-size:0.85rem; color:#64748b; font-weight:600; }

        .grid { flex:1; display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; padding:1.5rem 2rem; }

        .tile { background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:0.6rem; padding:1.75rem 1.5rem; text-decoration:none; transition:all 0.12s; min-height:185px; }
        .tile:hover { transform:translateY(-2px); box-shadow:0 6px 24px rgba(0,0,0,0.07); }
        .tile:active { transform:scale(0.98); box-shadow:none; }

        .tile-icon { font-size:2.6rem; line-height:1; }
        .tile-label { font-size:1.2rem; font-weight:800; letter-spacing:-0.02em; }
        .tile-sub { font-size:0.8rem; font-weight:600; color:#94a3b8; }

        .tile-blue  { border-color:#c7d7fe; } .tile-blue .tile-label  { color:#4338ca; }
        .tile-green { border-color:#bbf7d0; } .tile-green .tile-label { color:#059669; }
        .tile-amber { border-color:#fde68a; } .tile-amber .tile-label { color:#b45309; }
        .tile-sky   { border-color:#bae6fd; } .tile-sky .tile-label   { color:#0369a1; }
        .tile-blue:hover  { background:#f8f9ff; border-color:#818cf8; }
        .tile-green:hover { background:#f0fdf8; border-color:#34d399; }
        .tile-amber:hover { background:#fffbeb; border-color:#f59e0b; }
        .tile-sky:hover   { background:#f0f9ff; border-color:#38bdf8; }

        .footer { background:#fff; border-top:1px solid #e2e8f0; padding:0.65rem 2rem; display:flex; justify-content:flex-end; }
        .logout-btn { background:none; border:1px solid #e2e8f0; border-radius:8px; padding:0.4rem 1rem; font-size:0.82rem; font-weight:600; color:#64748b; cursor:pointer; font-family:inherit; transition:all 0.1s; }
        .logout-btn:hover { background:#f1f5f9; color:#334155; border-color:#cbd5e1; }
    </style>
</head>
<body>
    <div class="topbar">
        <span class="topbar-brand">📞 Call Center OMS</span>
        <span class="topbar-user">{{ Auth::user()->name }}</span>
    </div>

    <div class="grid">
        <a href="{{ route('app.call-centar') }}" class="tile tile-blue">
            <span class="tile-icon">🔍</span>
            <span class="tile-label">Call Centar</span>
            <span class="tile-sub">Pretraga i detalji porudžbina</span>
        </a>
        <a href="{{ route('app.pickup') }}" class="tile tile-green">
            <span class="tile-icon">📦</span>
            <span class="tile-label">Lično preuzimanje</span>
            <span class="tile-sub">Sken barkoda → Completed</span>
        </a>
        <a href="{{ route('app.send') }}" class="tile tile-amber">
            <span class="tile-icon">✉️</span>
            <span class="tile-label">Pošalji porudžbinu</span>
            <span class="tile-sub">Resend email kupcu</span>
        </a>
        <a href="{{ route('app.print') }}" class="tile tile-sky">
            <span class="tile-icon">🖨️</span>
            <span class="tile-label">Štampa</span>
            <span class="tile-sub">Neštampane, bulk, istorija</span>
        </a>
    </div>

    <div class="footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Odjavi se</button>
        </form>
    </div>
</body>
</html>
