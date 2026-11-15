<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" dir="<?php echo e(is_ur() ? 'rtl' : 'ltr'); ?>" data-theme="light">
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
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo $__env->yieldContent('title', __('Janwar Mart')); ?> — Janwar Mart</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Noto+Nastaliq+Urdu:wght@400;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/style.css">
<script>try{var t=localStorage.getItem('jm_theme');if(t==='dark'||t==='light')document.documentElement.dataset.theme=t}catch(e){}document.documentElement.classList.add('js')</script>
</head>
<body>
<header class="hd"><div class="hrow">
  <a class="logo" href="/" style="text-decoration:none"><img class="lg" alt="" src="/img/logo.png"><span id="brand"><?php echo e(__('Janwar Mart')); ?></span></a>
  <form class="srch" action="/animals" method="get" role="search">
    <div class="sbox"><input id="hs" name="q" type="search" value="<?php echo e(request('q')); ?>" autocomplete="off" placeholder="<?php echo e(__('Search animal or city…')); ?>" aria-label="<?php echo e(__('Search')); ?>"></div>
    <button type="button" class="ghost" id="mic" aria-label="<?php echo e(__('Voice search')); ?>" aria-pressed="false"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0014 0M12 18v3"/></svg></button>
    <button type="submit" aria-label="<?php echo e(__('Search')); ?>"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></button>
  </form>
  <div class="hic">
    <a class="ghost ib btn" href="/cart" aria-label="<?php echo e(__('Cart')); ?>" style="text-decoration:none">🛒<span class="bdg"><?php echo e(app(\App\Services\CartService::class)->count() ?: ''); ?></span></a>
    <a class="ghost ib btn" href="/favorites" aria-label="<?php echo e(__('Favorites')); ?>" style="text-decoration:none">♥</a>
    <button class="ghost ib" id="thb" type="button" aria-label="<?php echo e(__('Theme')); ?>">🌙</button>
    <?php if(auth()->guard()->check()): ?>
      <a class="ghost btn" id="authb" href="/profile" style="font-size:14px;padding:0 12px;text-decoration:none"><span>👤</span> <span class="atx"><?php echo e(\Illuminate\Support\Str::limit(auth()->user()->name, 12)); ?></span></a>
    <?php else: ?>
      <a class="ghost btn" id="authb" href="/login" style="font-size:14px;padding:0 12px;text-decoration:none"><span>🔐</span> <span class="atx"><?php echo e(__('Sign up / Log in')); ?></span></a>
    <?php endif; ?>
    <a class="ghost btn" id="langBtn" href="/lang/<?php echo e(is_ur() ? 'en' : 'ur'); ?>" style="text-decoration:none" aria-label="Language"><?php echo e(is_ur() ? 'English' : 'اردو'); ?></a>
    <details class="menu"><summary class="ghost ib btn" aria-label="<?php echo e(__('Menu')); ?>" style="list-style:none;cursor:pointer">☰</summary>
      <div class="menu-box">
        <a href="/animals">🐄 <?php echo e(__('Browse animals')); ?></a>
        <a href="/sell">➕ <?php echo e(__('Sell an animal')); ?></a>
        <?php if(auth()->guard()->check()): ?>
          <a href="/my-ads">📋 <?php echo e(__('My ads')); ?></a>
          <a href="/orders">🧾 <?php echo e(__('My orders')); ?></a>
          <a href="/favorites">♥ <?php echo e(__('Favorites')); ?></a>
          <a href="/consultations">💬 <?php echo e(__('My consultations')); ?></a>
          <a href="/profile">⚙ <?php echo e(__('Profile')); ?></a>
          <?php if(auth()->user()->doctor): ?><a href="/doctor/dashboard">🩺 <?php echo e(__('Doctor dashboard')); ?></a><?php endif; ?>
          <?php if(auth()->user()->isAdmin()): ?><a href="/admin">🛡 <?php echo e(__('Admin panel')); ?></a><?php endif; ?>
        <?php endif; ?>
        <a href="/doctors">🩺 <?php echo e(__('Vet doctors')); ?></a>
        <a href="/doctor/register">📝 <?php echo e(__('Register as a doctor')); ?></a>
        <a href="/plans">💎 <?php echo e(__('Plans')); ?></a>
        <a href="/help">🛟 <?php echo e(__('Help & safety')); ?></a>
        <a href="/contact">📞 <?php echo e(__('Contact admin')); ?></a>
        <a href="#" id="installBtn" hidden>⬇ <?php echo e(__('Install app')); ?></a>
        <?php if(auth()->guard()->check()): ?>
          <form method="post" action="/logout"><?php echo csrf_field(); ?><button class="ghost" style="width:100%">🚪 <?php echo e(__('Log out')); ?></button></form>
        <?php endif; ?>
      </div>
    </details>
  </div>
</div></header>

<main id="app" data-r="<?php echo $__env->yieldContent('route'); ?>">
  <?php if(session('ok')): ?><div class="note flash" role="status" style="margin-bottom:12px">✔ <?php echo e(session('ok')); ?></div><?php endif; ?>
  <?php if($errors->any()): ?><div class="note flash" role="alert" style="margin-bottom:12px;border-color:var(--err)"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="err">⚠ <?php echo e($e); ?></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>
  <?php echo $__env->yieldContent('content'); ?>
</main>
<footer id="ft"><b><?php echo e(__('Janwar Mart')); ?></b> — <?php echo e(__('Every ad is shown only after admin review.')); ?><br><small>© <?php echo e(date('Y')); ?> Janwar Mart</small></footer>
<nav class="nav" aria-label="Main">
  <a href="/" class="<?php echo e(request()->is('/') ? 'on' : ''); ?>"><b>🏠</b><?php echo e(__('Home')); ?></a>
  <a href="/animals" class="<?php echo e(request()->is('animals*') ? 'on' : ''); ?>"><b>🐄</b><?php echo e(__('Animals')); ?></a>
  <a href="/sell" class="sell"><b>➕</b><?php echo e(__('Sell')); ?></a>
  <a href="/cart" class="<?php echo e(request()->is('cart') ? 'on' : ''); ?>"><b>🛒</b><?php echo e(__('Cart')); ?></a>
  <a href="/contact" class="<?php echo e(request()->is('contact') ? 'on' : ''); ?>"><b>📞</b><?php echo e(__('Contact')); ?></a>
</nav>
<script src="/js/app.js" defer></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>