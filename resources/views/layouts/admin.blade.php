<!DOCTYPE html>
<html lang="en" dir="ltr" data-bs-theme="light">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><meta name="robots" content="noindex">
<title>Admin — Janwar Mart</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script>try{var t=localStorage.getItem('adm_theme');if(t)document.documentElement.setAttribute('data-bs-theme',t)}catch(e){}</script>
<style>
:root{--g:#216B45;--gd:#164B33;--gold:#F2B705;--side:#0b3d1c}
[data-bs-theme=dark]{--side:#0a120e}
body{background:var(--bs-tertiary-bg)}
.sidebar{width:260px;background:var(--side);color:#d7e6dc;position:fixed;inset:0 auto 0 0;display:flex;flex-direction:column;z-index:1040;overflow-y:auto;padding:14px 10px}
.sidebar .brand{display:flex;gap:10px;align-items:center;color:var(--gold);font-weight:700;padding:6px 8px 14px;border-bottom:1px solid #ffffff1f;margin-bottom:6px;text-decoration:none}
.sidebar .brand i{font-style:normal;display:grid;place-items:center;width:36px;height:36px;border-radius:10px;background:var(--gold);color:var(--side)}
.sidebar .grp{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#8fb09b;margin:14px 10px 4px}
.sidebar a.nv{display:flex;align-items:center;gap:10px;color:#d7e6dc;text-decoration:none;padding:9px 10px;border-radius:10px;margin:1px 0}
.sidebar a.nv:hover{background:#ffffff14}.sidebar a.nv.on{background:var(--gold);color:var(--side);font-weight:700}
.sidebar a.nv .ic{width:22px;text-align:center}.sidebar a.nv .bd{margin-left:auto;background:#d92d20;color:#fff;border-radius:99px;font-size:11px;min-width:20px;text-align:center;padding:0 6px;line-height:18px}
.sidebar .who{margin-top:auto;padding:12px 10px 4px;font-size:12px;color:#8fb09b;border-top:1px solid #ffffff1f}
.content{margin-left:260px;min-height:100vh}
.topbar{position:sticky;top:0;z-index:1030;background:var(--bs-body-bg);border-bottom:1px solid var(--bs-border-color);padding:10px 16px;display:flex;gap:10px;align-items:center}
.topbar h1{font-size:18px;margin:0;flex:1}.topbar .form-control{max-width:260px}
.hello{background:linear-gradient(135deg,#0b3d1c,#216B45);color:#fff;border-radius:16px;border-bottom:3px solid var(--gold)}
.kpi{border:1px solid var(--bs-border-color);border-radius:14px;background:var(--bs-body-bg);padding:14px;display:flex;gap:12px;align-items:center;text-decoration:none;color:inherit;transition:.15s}.kpi:hover{transform:translateY(-2px);box-shadow:0 8px 20px #0002;color:inherit}
.kpi .ki{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;font-size:20px;background:rgba(33,107,69,.14);flex:none}.kpi b{display:block;font-size:24px;line-height:1.1}.kpi span{font-size:12px;color:var(--bs-secondary-color)}
.kpi.warn .ki{background:#fdecc8}.kpi.bad .ki{background:#fde0dd}
.thumb{width:48px;height:48px;object-fit:cover;border-radius:8px}.table td,.table th{vertical-align:top}
.btn-jm{background:var(--g);color:#fff}.btn-jm:hover{background:var(--gd);color:#fff}
.stat{border:1px solid var(--bs-border-color);border-top:3px solid var(--gold);border-radius:12px;padding:14px;text-align:center;background:var(--bs-body-bg)}.stat b{display:block;font-size:26px;color:var(--g)}
@media(max-width:991px){.sidebar{transform:translateX(-102%);transition:transform .25s}.sidebar.open{transform:none;box-shadow:0 0 40px #0008}.content{margin-left:0}}
</style>
</head>
<body>
@php
$nav = [
 'Dashboard' => [['overview', '📊', 'Overview']],
 'Marketplace' => [['ads', '🐄', 'Ads'], ['orders', '🧾', 'Purchase requests'], ['sold', '✅', 'Sold / Bought report']],
 'People' => [['users', '👥', 'Users'], ['logins', '🔐', 'Login activity']],
 'Vets' => [['doctors', '🩺', 'Vet doctors'], ['consultations', '💬', 'Consultations']],
 'Money' => [['payments', '💳', 'Payments & COD']],
 'Support' => [['messages', '✉', 'Contact messages'], ['reports', '🚩', 'Ad reports'], ['disputes', '⚖', 'Disputes & meetings']],
 'System' => [['notifications', '🔔', 'Notifications'], ['audit', '📜', 'Admin log']],
];
@endphp
<aside class="sidebar" id="side" aria-label="Admin menu">
  <a class="brand" href="/admin"><i>🐄</i><span>Janwar Mart<br><small class="fw-normal" style="color:#8fb09b">Admin console</small></span></a>
  @foreach($nav as $group => $items)
    <div class="grp">{{ $group }}</div>
    @foreach($items as [$k, $ic, $t])
      <a class="nv {{ $tab === $k ? 'on' : '' }}" href="{{ $k === 'overview' ? '/admin' : '/admin/tab/'.$k }}"><span class="ic">{{ $ic }}</span>{{ $t }} @if(!empty($badges[$k]))<span class="bd">{{ $badges[$k] }}</span>@endif</a>
    @endforeach
  @endforeach
  <div class="who">Signed in as <b>{{ auth()->user()->name }}</b><br><a href="/" target="_blank" style="color:var(--gold)">View website ↗</a></div>
</aside>
<div class="content">
  <div class="topbar">
    <button class="btn btn-outline-secondary d-lg-none" onclick="document.getElementById('side').classList.toggle('open')" aria-label="Menu">☰</button>
    <h1>{{ $tabs[$tab] }}</h1>
    <input id="qf" class="form-control form-control-sm" type="search" placeholder="Quick find in this page…">
    <a class="btn btn-outline-secondary position-relative" href="/admin/tab/notifications" title="Notifications">🔔 @if(!empty($badges['notifications']))<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $badges['notifications'] }}</span>@endif</a>
    <button class="btn btn-outline-secondary" id="themeBtn" title="Light / dark">🌓</button>
    <form method="post" action="/logout">@csrf<button class="btn btn-warning">Log out</button></form>
  </div>
  <main class="p-3">
    @if(session('ok'))<div class="alert alert-success py-2">✔ {{ session('ok') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger py-2">@foreach($errors->all() as $e)<div>⚠ {{ $e }}</div>@endforeach</div>@endif
    @yield('content')
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('themeBtn').onclick=function(){var h=document.documentElement,n=h.getAttribute('data-bs-theme')==='dark'?'light':'dark';h.setAttribute('data-bs-theme',n);try{localStorage.setItem('adm_theme',n)}catch(e){}};
document.getElementById('qf').oninput=function(){var v=this.value.toLowerCase();document.querySelectorAll('main table tr').forEach(function(r){if(r.querySelector('th'))return;r.style.display=!v||r.textContent.toLowerCase().indexOf(v)>-1?'':'none'})};
</script>
@stack('scripts')
</body></html>
