<?php $__env->startSection('route', 'profile'); ?>
<?php $__env->startSection('content'); ?>
<h2>⚙ <?php echo e(__('Profile')); ?></h2>
<div class="panel"><div class="kv"><span><?php echo e(__('Name')); ?></span><b><?php echo e($u->name); ?></b><span><?php echo e(__('Phone')); ?></span><b dir="ltr"><?php echo e($u->phone); ?></b><span><?php echo e(__('Email')); ?></span><b><?php echo e($u->email ?: '—'); ?></b><span><?php echo e(__('Role')); ?></span><b><?php echo e(__('r_'.$u->role)); ?></b><span><?php echo e(__('Plan')); ?></span><b><?php echo e($u->plan); ?> (<?php echo e($u->adLimit()); ?> <?php echo e(__('ads')); ?>)</b></div>
<div class="acts" style="margin-top:12px"><a class="btn" href="/my-ads">📋 <?php echo e(__('My ads')); ?></a><a class="btn ghost" href="/orders">🧾 <?php echo e(__('My orders')); ?></a><a class="btn ghost" href="/plans">💎 <?php echo e(__('Plans')); ?></a><a class="btn ghost" href="/favorites">♥ <?php echo e(__('Favorites')); ?></a></div>
<form method="post" action="/logout" style="margin-top:12px"><?php echo csrf_field(); ?><button class="ghost">🚪 <?php echo e(__('Log out')); ?></button></form></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/profile.blade.php ENDPATH**/ ?>