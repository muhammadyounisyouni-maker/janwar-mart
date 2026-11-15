<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_ur() ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="theme-color" content="#0b6b4a">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" type="image/png" sizes="48x48" href="/icons/favicon-48.png">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Janwar Mart">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', __('Janwar Mart')) — Janwar Mart</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/style.css">
<script>try{var t=localStorage.getItem('jm_theme');if(t==='dark'||t==='light')document.documentElement.dataset.theme=t}catch(e){}document.documentElement.classList.add('js')</script>
</head>
<body>
<header class="hd"><div class="hrow">
  <a class="logo" href="/" style="text-decoration:none"><img class="lg" alt="" src="/img/logo.png"><span id="brand">{{ __('Janwar Mart') }}</span></a>
  <form class="srch" action="/animals" method="get" role="search">
    <div class="sbox"><input id="hs" name="q" type="search" value="{{ request('q') }}" autocomplete="off" placeholder="{{ __('Search animal or city…') }}" aria-label="{{ __('Search') }}"></div>
    <button type="button" class="ghost" id="mic" aria-label="{{ __('Voice search') }}" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0014 0M12 18v3"/></svg></button>
    <button type="submit" aria-label="{{ __('Search') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button>
  </form>
  <div class="hic">
    <a class="ghost ib btn" href="/cart" aria-label="{{ __('Cart') }}" style="text-decoration:none">🛒<span class="bdg">{{ app(\App\Services\CartService::class)->count() ?: '' }}</span></a>
    <a class="ghost ib btn" href="/favorites" aria-label="{{ __('Favorites') }}" style="text-decoration:none">♥</a>
    <button class="ghost ib" id="thb" type="button" aria-label="{{ __('Theme') }}">🌙</button>
    @auth
      <a class="ghost btn" id="authb" href="/profile" style="font-size:14px;padding:0 12px;text-decoration:none"><span>👤</span> <span class="atx">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 12) }}</span></a>
    @else
      <a class="ghost btn" id="authb" href="/login" style="font-size:14px;padding:0 12px;text-decoration:none"><span>🔐</span> <span class="atx">{{ __('Sign up / Log in') }}</span></a>
    @endauth
    <a class="ghost btn" id="langBtn" href="/lang/{{ is_ur() ? 'en' : 'ur' }}" style="text-decoration:none" aria-label="Language">{{ is_ur() ? 'English' : 'اردو' }}</a>
    <details class="menu"><summary class="ghost ib btn" aria-label="{{ __('Menu') }}" style="list-style:none;cursor:pointer">☰</summary>
      <div class="menu-box">
        <a href="/animals">🐄 {{ __('Browse animals') }}</a>
        <a href="/sell">➕ {{ __('Sell an animal') }}</a>
        @auth
          <a href="/my-ads">📋 {{ __('My ads') }}</a>
          <a href="/orders">🧾 {{ __('My orders') }}</a>
          <a href="/favorites">♥ {{ __('Favorites') }}</a>
          <a href="/consultations">💬 {{ __('My consultations') }}</a>
          <a href="/profile">⚙ {{ __('Profile') }}</a>
          @if(auth()->user()->doctor)<a href="/doctor/dashboard">🩺 {{ __('Doctor dashboard') }}</a>@endif
          @if(auth()->user()->isAdmin())<a href="/admin">🛡 {{ __('Admin panel') }}</a>@endif
        @endauth
        <a href="/doctors">🩺 {{ __('Vet doctors') }}</a>
        <a href="/doctor/register">📝 {{ __('Register as a doctor') }}</a>
        <a href="/plans">💎 {{ __('Plans') }}</a>
        <a href="/help">🛟 {{ __('Help & safety') }}</a>
        <a href="/contact">📞 {{ __('Contact admin') }}</a>
        <a href="#" id="installBtn" hidden>⬇ {{ __('Install app') }}</a>
        @auth
          <form method="post" action="/logout">@csrf<button class="ghost" style="width:100%">🚪 {{ __('Log out') }}</button></form>
        @endauth
      </div>
    </details>
  </div>
</div></header>

<main id="app" data-r="@yield('route')">
  @if(session('ok'))<div class="note flash" role="status" style="margin-bottom:12px">✔ {{ session('ok') }}</div>@endif
  @if($errors->any())<div class="note flash" role="alert" style="margin-bottom:12px;border-color:var(--err)">@foreach($errors->all() as $e)<div class="err">⚠ {{ $e }}</div>@endforeach</div>@endif
  @yield('content')
</main>
<footer id="ft"><b>{{ __('Janwar Mart') }}</b> — {{ __('Every ad is shown only after admin review.') }}<br><small>© {{ date('Y') }} Janwar Mart</small></footer>
<nav class="nav" aria-label="Main">
  <a href="/" class="{{ request()->is('/') ? 'on' : '' }}"><b>🏠</b>{{ __('Home') }}</a>
  <a href="/animals" class="{{ request()->is('animals*') ? 'on' : '' }}"><b>🐄</b>{{ __('Animals') }}</a>
  <a href="/sell" class="sell"><b>➕</b>{{ __('Sell') }}</a>
  <a href="/cart" class="{{ request()->is('cart') ? 'on' : '' }}"><b>🛒</b>{{ __('Cart') }}</a>
  <a href="/contact" class="{{ request()->is('contact') ? 'on' : '' }}"><b>📞</b>{{ __('Contact') }}</a>
</nav>
<script src="/js/app.js" defer></script>
</body>
</html>
