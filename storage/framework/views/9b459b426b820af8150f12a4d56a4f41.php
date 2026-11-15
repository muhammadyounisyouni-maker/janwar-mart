<?php $__env->startSection('route', 'auth'); ?>
<?php $__env->startSection('content'); ?>
<h2>🔐 <?php echo e(__('Account')); ?></h2>
<div class="row2"><a class="btn <?php echo e($tab === 'signup' ? '' : 'ghost'); ?>" href="/register"><?php echo e(__('Sign up')); ?></a><a class="btn <?php echo e($tab === 'login' ? '' : 'ghost'); ?>" href="/login"><?php echo e(__('Log in')); ?></a></div>
<?php if($tab === 'signup'): ?>
<form class="panel" method="post" action="/register" style="display:grid;gap:10px;margin-top:12px"><?php echo csrf_field(); ?>
  <input name="name" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(__('Your name')); ?> *" required>
  <input id="phone" name="phone" type="tel" inputmode="tel" value="<?php echo e(old('phone')); ?>" placeholder="<?php echo e(__('Phone')); ?> (03XXXXXXXXX) *" required>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px"><input name="otp" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="<?php echo e(__('Verification code (6 digits)')); ?> *" required><button type="button" class="ghost" data-otp="signup" data-phone="#phone" data-sent="<?php echo e(__('Code sent by SMS')); ?>"><?php echo e(__('Send code')); ?></button></div>
  <small id="otpmsg" role="status"></small>
  <input name="email" type="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('Email (optional)')); ?>">
  <input name="password" type="password" autocomplete="new-password" minlength="6" placeholder="<?php echo e(__('Password (at least 6 characters)')); ?> *" required>
  <div class="roles"><b><?php echo e(__('Who are you?')); ?> *</b>
    <?php $__currentLoopData = ['buyer' => '🛒 '.__('I want to buy animals'), 'seller' => '🐄 '.__('I want to sell animals'), 'doctor' => '🩺 '.__('I am a vet doctor')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><label class="pmo"><input type="radio" name="role" value="<?php echo e($v); ?>" <?php if(old('role', 'buyer') === $v): echo 'checked'; endif; ?>> <?php echo e($t); ?></label><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div>
  <label class="pmo"><input type="checkbox" name="enable_2fa" value="1" <?php if(old('enable_2fa')): echo 'checked'; endif; ?>> 🔐 <?php echo e(__('Protect my account with Google Authenticator (two-factor login)')); ?></label>
  <button><?php echo e(__('Sign up')); ?></button><small><?php echo e(__('Your signup and login are recorded for safety (admin can see them).')); ?></small>
</form>
<?php elseif($tab === 'reset'): ?>
<form class="panel" method="post" action="/password/reset" style="display:grid;gap:10px;margin-top:12px"><?php echo csrf_field(); ?>
  <h3 style="margin:0">🔑 <?php echo e(__('Reset password')); ?></h3>
  <input id="phone" name="phone" type="tel" value="<?php echo e(old('phone')); ?>" placeholder="<?php echo e(__('Phone')); ?> (03XXXXXXXXX) *" required>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px"><input name="otp" inputmode="numeric" maxlength="6" placeholder="<?php echo e(__('Verification code (6 digits)')); ?>" required><button type="button" class="ghost" data-otp="reset" data-phone="#phone" data-sent="<?php echo e(__('If this phone is registered, a code was sent')); ?>"><?php echo e(__('Send code')); ?></button></div>
  <small id="otpmsg" role="status"></small>
  <input name="password" type="password" minlength="6" autocomplete="new-password" placeholder="<?php echo e(__('New password')); ?> *" required>
  <button><?php echo e(__('Reset password')); ?></button>
</form>
<?php else: ?>
<form class="panel" method="post" action="/login" style="display:grid;gap:10px;margin-top:12px"><?php echo csrf_field(); ?>
  <input name="login" value="<?php echo e(old('login')); ?>" placeholder="<?php echo e(__('Phone or email')); ?>" required>
  <input name="password" type="password" autocomplete="current-password" placeholder="<?php echo e(__('Password')); ?>" required>
  <label class="pmo"><input type="checkbox" name="remember" value="1"> <?php echo e(__('Keep me logged in')); ?></label>
  <button><?php echo e(__('Log in')); ?></button><a class="btn ghost" href="/password/reset"><?php echo e(__('Forgot password?')); ?></a>
  <small>🔐 <?php echo e(__('If you turned on two-factor login, you will be asked for the code from Google Authenticator next.')); ?></small>
</form>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/auth.blade.php ENDPATH**/ ?>