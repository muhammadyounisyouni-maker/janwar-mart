<?php $__env->startSection('route', 'cart'); ?>
<?php $__env->startSection('content'); ?>
<h2>🛒 <?php echo e(__('Cart')); ?></h2>
<ol class="stp"><li class="on">1. <?php echo e(__('Cart')); ?></li><li>2. <?php echo e(__('Your details & payment')); ?></li><li>3. <?php echo e(__('Admin confirmation')); ?></li></ol>
<?php if($items->count()): ?>
  <div class="panel">
  <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row3"><span class="ic"><?php if($l->cover()): ?><img src="<?php echo e($l->cover()); ?>" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:12px"><?php else: ?><i class="pi pic-<?php echo e($l->category?->key); ?>"></i><?php endif; ?></span>
      <div><b><?php echo e($l->displayTitle()); ?></b><br><small>📍 <?php echo e(city_name($l->city)); ?> · <span dir="ltr"><?php echo e($l->ref); ?></span></small></div>
      <b><?php echo e(__('PKR')); ?> <?php echo e(pkr($l->price)); ?></b>
      <form method="post" action="/cart/<?php echo e($l->ref); ?>"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="ghost" aria-label="<?php echo e(__('Remove')); ?>">✕</button></form></div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <div class="tot"><span><?php echo e(__('Total')); ?></span><b><?php echo e(__('PKR')); ?> <?php echo e(pkr($items->sum('price'))); ?></b></div>
  <a class="btn" href="/checkout" style="margin-top:12px;width:100%"><?php echo e(__('Continue')); ?> ›</a></div>
<?php else: ?><div class="panel" style="text-align:center"><?php echo e(__('Your cart is empty.')); ?><br><a class="btn" href="/animals" style="margin-top:10px"><?php echo e(__('Browse animals')); ?></a></div><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/cart.blade.php ENDPATH**/ ?>