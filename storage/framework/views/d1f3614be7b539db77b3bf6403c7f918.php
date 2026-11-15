<?php $__env->startSection('route', 'browse'); ?>
<?php $__env->startSection('title', __('Animals')); ?>
<?php $__env->startSection('content'); ?>
<div class="chips">
  <a class="btn chip <?php echo e(!request('cat') || request('cat') === 'all' ? 'on' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['cat' => null, 'page' => null])); ?>"><?php echo e(__('All')); ?></a>
  <?php $__currentLoopData = $cats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><a class="btn chip <?php echo e(request('cat') === $c->key ? 'on' : ''); ?>" href="<?php echo e(request()->fullUrlWithQuery(['cat' => $c->key, 'page' => null])); ?>"><i class="ci pic-<?php echo e($c->key); ?>"></i><?php echo e($c->label()); ?></a><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="shop">
<aside><form class="panel" method="get" action="/animals" style="display:grid;gap:10px">
  <h3 style="margin:0">⚙ <?php echo e(__('Filters')); ?></h3>
  <input type="hidden" name="cat" value="<?php echo e(request('cat')); ?>">
  <input name="q" value="<?php echo e(request('q')); ?>" placeholder="<?php echo e(__('Search animal or city…')); ?>">
  <select name="city" aria-label="<?php echo e(__('City')); ?>"><option value="all"><?php echo e(__('All cities')); ?></option><?php $__currentLoopData = config('janwar.cities'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($k); ?>" <?php if(request('city') === $k): echo 'selected'; endif; ?>><?php echo e(city_name($k)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
  <div class="row2"><input type="number" name="min" value="<?php echo e(request('min')); ?>" placeholder="<?php echo e(__('Min price')); ?>" min="0"><input type="number" name="max" value="<?php echo e(request('max')); ?>" placeholder="<?php echo e(__('Max price')); ?>" min="0"></div>
  <select name="sort" aria-label="<?php echo e(__('Sort')); ?>"><option value=""><?php echo e(__('Newest')); ?></option><option value="low" <?php if(request('sort') === 'low'): echo 'selected'; endif; ?>><?php echo e(__('Lowest price')); ?></option><option value="high" <?php if(request('sort') === 'high'): echo 'selected'; endif; ?>><?php echo e(__('Highest price')); ?></option></select>
  <button><?php echo e(__('Apply')); ?></button><a class="btn ghost" href="/animals"><?php echo e(__('Clear')); ?></a>
</form></aside>
<section>
  <p><b><?php echo e($list->total()); ?></b> <?php echo e(__('animals found')); ?></p>
  <?php if($list->count()): ?><div class="grid"><?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo $__env->make('partials.card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><div class="pager"><?php echo e($list->links()); ?></div>
  <?php else: ?><div class="panel" style="text-align:center"><?php echo e(__('No animals found. Try changing the filters.')); ?></div><?php endif; ?>
</section></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/pages/browse.blade.php ENDPATH**/ ?>