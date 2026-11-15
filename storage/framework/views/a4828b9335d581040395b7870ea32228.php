<?php $__env->startSection('route', 'contact'); ?>
<?php $__env->startSection('content'); ?>
<?php $num = config('janwar.contact_number'); ?>
<h2>📞 <?php echo e(__('Contact admin')); ?></h2>
<div class="panel"><p><?php echo e(__('Problem with an ad, a seller or your account? Contact the admin.')); ?></p>
  <div class="acts"><a class="btn" href="tel:+<?php echo e($num); ?>">📞 <?php echo e(__('Call')); ?></a><a class="btn wa" target="_blank" rel="noopener" href="https://wa.me/<?php echo e($num); ?>">💬 WhatsApp</a></div></div>
<form class="panel" method="post" action="/contact" style="margin-top:12px;display:grid;gap:10px"><?php echo csrf_field(); ?>
  <select name="type"><?php $__currentLoopData = ['ad_issue' => __('Problem with an ad'), 'seller_complaint' => __('Complaint about a seller'), 'account_login' => __('Account / login problem'), 'other' => __('Other')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($v); ?>" <?php if(old('type') === $v): echo 'selected'; endif; ?>><?php echo e($t); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
  <input name="ad_ref" value="<?php echo e(old('ad_ref')); ?>" placeholder="<?php echo e(__('Ad reference (optional), e.g. JM-1101')); ?>">
  <textarea name="message" rows="4" required maxlength="1000" placeholder="<?php echo e(__('Your message')); ?> *"><?php echo e(old('message')); ?></textarea>
  <input name="name" value="<?php echo e(old('name', auth()->user()->name ?? '')); ?>" placeholder="<?php echo e(__('Your name')); ?>">
  <input name="phone" type="tel" value="<?php echo e(old('phone', auth()->user()->phone ?? '')); ?>" placeholder="03XXXXXXXXX *" required>
  <button><?php echo e(__('Send to admin')); ?></button></form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/contact.blade.php ENDPATH**/ ?>