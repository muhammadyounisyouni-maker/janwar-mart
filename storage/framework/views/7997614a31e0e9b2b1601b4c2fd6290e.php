<?php $__env->startSection('route', 'orders'); ?>
<?php $__env->startSection('content'); ?>
<h2>🧾 <?php echo e(__('My orders')); ?></h2>
<?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="panel" style="margin-bottom:10px">
  <b dir="ltr"><?php echo e($o->ref); ?></b> <span class="tag"><?php echo e(__('o_'.$o->status)); ?></span> <span class="tag"><?php echo e(__('p_'.$o->pay_status)); ?></span> <?php if($o->admin_present): ?><span class="tag">👤 <?php echo e(__('Admin present')); ?></span><?php endif; ?>
  <p><?php $__currentLoopData = $o->listings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a href="/animals/<?php echo e($l->ref); ?>"><?php echo e($l->displayTitle()); ?></a> (<?php echo e(pkr($l->pivot->price)); ?>)<?php if(!$loop->last): ?>, <?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></p>
  <p><b><?php echo e(__('Total')); ?>: <?php echo e(__('PKR')); ?> <?php echo e(pkr($o->total)); ?></b> · <?php echo e($o->pay_method === 'cod' ? __('Cash on delivery') : strtoupper($o->pay_method)); ?></p>
  <?php if($o->meeting): ?><div class="note">📅 <?php echo e(__('Meeting')); ?>: <?php echo e($o->meeting->meet_at); ?> · <?php echo e($o->meeting->place); ?> · <?php echo e($o->meeting->admin_name); ?></div><?php endif; ?>
  <?php if($o->dispute): ?><div class="note">⚖ <?php echo e(__('Dispute')); ?>: <?php echo e(__('d_'.$o->dispute->status)); ?> <?php if($o->dispute->resolution): ?>— <?php echo e($o->dispute->resolution); ?><?php endif; ?></div>
  <?php elseif($o->status === 'completed'): ?>
    <details><summary class="btn ghost" style="list-style:none">⚖ <?php echo e(__('Problem with this purchase? Ask for a refund')); ?></summary>
      <form method="post" action="/orders/<?php echo e($o->id); ?>/dispute" style="display:grid;gap:8px;margin-top:8px"><?php echo csrf_field(); ?><textarea name="reason" rows="3" required minlength="10" placeholder="<?php echo e(__('Explain the problem (at least 10 characters)')); ?>"></textarea><button><?php echo e(__('Send to admin')); ?></button></form></details>
  <?php endif; ?>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><div class="panel" style="text-align:center"><?php echo e(__('No orders yet.')); ?></div><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/orders.blade.php ENDPATH**/ ?>