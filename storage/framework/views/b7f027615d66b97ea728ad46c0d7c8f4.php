<?php $__env->startSection('route', 'home'); ?>
<?php $__env->startSection('content'); ?>
<?php $bw = explode(' ', __('Janwar Mart')); ?>
<div class="home">
<section class="land bleed bgimg"><div class="land-in"><div class="lt rv">
  <span class="chip2">🛡 <?php echo e(__('Admin-verified ads')); ?></span>
  <div class="wm"><?php echo e($bw[0]); ?> <b><?php echo e($bw[1] ?? ''); ?></b></div>
  <h1><?php echo e(__('Buy or sell your animal')); ?></h1><p><?php echo e(__('Livestock trading made easy')); ?></p>
  <div class="row"><a class="btn gold" href="/animals">🛒 <?php echo e(__('Buy animals')); ?></a><a class="btn btn2 goldo" href="/sell">🤝 <?php echo e(__('Sell animal')); ?></a></div>
  <div class="mini"><span>✔ <?php echo e(__('Verified sellers')); ?></span><span>📞 <?php echo e(__('Call or WhatsApp')); ?></span><span>🩺 <?php echo e(__('Verified vet doctors')); ?></span></div>
</div></div>
<div class="stats rv"><div class="panel"><b><?php echo e($nActive); ?></b><?php echo e(__('Animals for sale')); ?></div><div class="panel"><b><?php echo e($nVerified); ?></b><?php echo e(__('Verified ads')); ?></div><div class="panel"><b><?php echo e($nDoctors); ?></b><?php echo e(__('Verified doctors')); ?></div></div>
</section>
<div class="mq bleed" aria-hidden="true"><div class="mq-in">
  <?php for($i = 0; $i < 4; $i++): ?><?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span><?php echo e($c->icon); ?> <?php echo e($c->label()); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php $__currentLoopData = config('janwar.cities'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span>📍 <?php echo e(city_name($k)); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> <?php endfor; ?>
</div></div>
<div class="tr rv" style="margin-top:18px">
  <?php $__currentLoopData = [['🛡', __('Admin-verified ads')], ['✔', __('Verified sellers')], ['📞', __('Call or WhatsApp')], ['🩺', __('Verified vet doctors')]]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$i, $x]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><div class="panel"><span style="font-size:24px"><?php echo e($i); ?></span><br><b><?php echo e($x); ?></b></div><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="sh rv"><h2><?php echo e(__('Shop by animal')); ?></h2></div>
<div class="catrow rv"><?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="btn cc" href="/animals?cat=<?php echo e($c->key); ?>"><span class="pp"><i class="pop3 pop-<?php echo e($c->key); ?>"></i></span><b><?php echo e($c->label()); ?></b><small><?php echo e($c->ads); ?> <?php echo e(__('ads')); ?></small></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
<div class="sh rv"><h2><?php echo e(__('Shop by budget')); ?></h2></div>
<div class="bud rv"><a class="btn" href="/animals?max=100000" style="background:var(--card);color:var(--g);border:2px solid var(--g)"><?php echo e(__('Under 100,000')); ?></a><a class="btn" href="/animals?min=100000&max=300000" style="background:var(--card);color:var(--g);border:2px solid var(--g)">100,000 – 300,000</a><a class="btn" href="/animals?min=300000" style="background:var(--card);color:var(--g);border:2px solid var(--g)"><?php echo e(__('Above 300,000')); ?></a></div>
<div class="sh rv"><h2><?php echo e(__('Recently added')); ?></h2><a href="/animals"><?php echo e(__('View all')); ?> ›</a></div>
<?php if($recent->count()): ?><div class="grid rv"><?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo $__env->make('partials.card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php else: ?><div class="panel" style="text-align:center"><?php echo e(__('No animals found yet.')); ?></div><?php endif; ?>
<div class="duo rv"><div class="d1"><span class="big">🤝</span><h3><?php echo e(__('Have an animal to sell?')); ?></h3><p><?php echo e(__('Post a free ad in a few minutes.')); ?></p><a class="btn w" href="/sell"><?php echo e(__('Sell animal')); ?></a></div>
<div class="d2"><span class="big">🩺</span><h3><?php echo e(__('Vet doctors')); ?></h3><p><?php echo e(__('Ask an admin-verified doctor about your animal.')); ?></p><a class="btn w" href="/doctors"><?php echo e(__('Go')); ?></a></div></div>
<?php if($verified->count()): ?><div class="sh rv"><h2>✔ <?php echo e(__('Verified sellers')); ?></h2></div><div class="grid rv"><?php $__currentLoopData = $verified; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo $__env->make('partials.card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>
<div class="sh rv"><h2><?php echo e(__('How it works')); ?></h2></div>
<div class="how rv">
  <div class="panel"><h3 style="margin:0 0 8px"><?php echo e(__('Buy animals')); ?></h3><?php $__currentLoopData = [__('Choose an animal'), __('Talk by phone or WhatsApp'), __('Buy the animal with the admin present')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p style="margin:6px 0"><span class="num"><?php echo e($i + 1); ?></span><?php echo e($s); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
  <div class="panel"><h3 style="margin:0 0 8px"><?php echo e(__('Sell animal')); ?></h3><?php $__currentLoopData = [__('Post an ad'), __('Add photo and price'), __('Buyers will call you')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p style="margin:6px 0"><span class="num"><?php echo e($i + 1); ?></span><?php echo e($s); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
</div>
<div class="note" style="margin-top:16px"><?php echo e(__('Inspect the animal carefully and verify ownership, health and vaccination before paying.')); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/home.blade.php ENDPATH**/ ?>