<?php $__env->startSection('content'); ?>
<?php
$s = $stats; $todo = $s['pending'] + $s['doctors_pending'] + $s['open_orders']; $h = now()->hour;
$k = fn ($i, $n, $t, $url, $c = '') => "<div class='col-6 col-md-4 col-xl-2'><a class='kpi $c' href='$url'><div class='ki'>$i</div><div><b>$n</b><span>$t</span></div></a></div>";
?>
<div class="hello p-3 p-md-4 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
  <div><h2 class="h4 mb-1"><?php echo e($h < 12 ? 'Good morning' : ($h < 18 ? 'Good afternoon' : 'Good evening')); ?>, <?php echo e(auth()->user()->name); ?> 👋</h2>
    <div class="opacity-75"><?php echo e($todo ? "You have $todo item".($todo > 1 ? 's' : '').' waiting for your action.' : 'Everything is up to date. Nice work!'); ?></div></div>
  <div class="d-flex gap-2"><a class="btn btn-warning fw-bold" href="/admin/tab/ads">Review ads</a><a class="btn btn-warning fw-bold" href="/admin/tab/orders">Open orders</a></div>
</div>
<div class="row g-2 mb-3">
<?php echo $k('👥', $s['users'], 'Members', '/admin/tab/users'); ?><?php echo $k('🛒', $s['buyers'], 'Buyers', '/admin/tab/users'); ?><?php echo $k('🐄', $s['sellers'], 'Sellers', '/admin/tab/users'); ?><?php echo $k('🩺', $s['doctors'], 'Doctors', '/admin/tab/doctors'); ?>

<?php echo $k('⏳', $s['pending'], 'Ads waiting approval', '/admin/tab/ads', $s['pending'] ? 'warn' : ''); ?><?php echo $k('📢', $s['active'], 'Live on homepage', '/admin/tab/ads'); ?>

<?php echo $k('🧾', $s['open_orders'], 'Open purchase requests', '/admin/tab/orders', $s['open_orders'] ? 'warn' : ''); ?><?php echo $k('✅', $s['sold'], 'Animals sold (bought)', '/admin/tab/sold'); ?><?php echo $k('💰', 'Rs '.number_format($s['revenue']), 'Sales value', '/admin/tab/sold'); ?>

<?php echo $k('🔑', $s['logins_today'], 'Logins / signups today', '/admin/tab/logins'); ?><?php echo $k('⚠', $s['failed_today'], 'Failed logins today', '/admin/tab/logins', $s['failed_today'] ? 'bad' : ''); ?><?php echo $k('💵', $s['cod'], 'Cash-on-delivery orders', '/admin/tab/payments'); ?>

</div>
<div class="row g-3 mb-3">
  <div class="col-lg-6"><div class="card h-100"><div class="card-header fw-semibold">Activity — last 7 days</div><div class="card-body"><canvas id="cAct" height="130"></canvas></div></div></div>
  <div class="col-md-6 col-lg-3"><div class="card h-100"><div class="card-header fw-semibold">Ads by status</div><div class="card-body"><canvas id="cStatus" height="170"></canvas></div></div></div>
  <div class="col-md-6 col-lg-3"><div class="card h-100"><div class="card-header fw-semibold">By category</div><div class="card-body"><canvas id="cCat" height="170"></canvas></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">⏳ Ads to review</span><a href="/admin/tab/ads">all ›</a></div><ul class="list-group list-group-flush">
    <?php $__empty_1 = true; $__currentLoopData = $pendingAds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><li class="list-group-item d-flex justify-content-between align-items-center gap-2"><span><?php if($a->cover()): ?><img class="thumb me-2" style="width:40px;height:32px" src="<?php echo e($a->cover()); ?>" alt=""><?php endif; ?><b><?php echo e($a->displayTitle()); ?></b><br><small class="text-secondary"><?php echo e($a->ref); ?> · Rs <?php echo e(number_format($a->price)); ?></small></span>
      <span class="d-flex gap-1"><form method="post" action="/admin/listings/<?php echo e($a->id); ?>/approve"><?php echo csrf_field(); ?><button class="btn btn-sm btn-jm">Approve</button></form><form method="post" action="/admin/listings/<?php echo e($a->id); ?>/reject"><?php echo csrf_field(); ?><button class="btn btn-sm btn-outline-danger">Reject</button></form></span></li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><li class="list-group-item text-secondary">Nothing waiting 🎉</li><?php endif; ?></ul></div></div>
  <div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">🧾 Open requests & 🩺 doctors</span><a href="/admin/tab/orders">all ›</a></div><ul class="list-group list-group-flush">
    <?php $__currentLoopData = $openOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li class="list-group-item d-flex justify-content-between"><span><b><?php echo e($o->ref); ?></b> <small class="text-secondary"><?php echo e($o->name); ?> · Rs <?php echo e(number_format($o->total)); ?></small></span><span class="badge text-bg-<?php echo e($o->pay_status === 'confirmed' ? 'success' : 'warning'); ?>"><?php echo e($o->pay_status); ?></span></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $pendingDocs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li class="list-group-item d-flex justify-content-between"><span>🩺 <b><?php echo e($d->name); ?></b> <small class="text-secondary"><?php echo e($d->clinic); ?></small></span><a class="btn btn-sm btn-outline-secondary" href="/admin/tab/doctors">Verify</a></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php if(!$openOrders->count() && !$pendingDocs->count()): ?><li class="list-group-item text-secondary">No open requests</li><?php endif; ?></ul></div></div>
  <div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">🔔 Recent activity</span><a href="/admin/tab/notifications">all ›</a></div><ul class="list-group list-group-flush">
    <?php $__empty_1 = true; $__currentLoopData = $feed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><li class="list-group-item"><?php echo e($n->icon); ?> <?php if (! ($n->is_read)): ?><b>• </b><?php endif; ?><?php echo e($n->text); ?><br><small class="text-secondary"><?php echo e($n->created_at?->diffForHumans()); ?></small></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><li class="list-group-item text-secondary">No activity yet</li><?php endif; ?></ul></div></div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){var d=<?php echo json_encode($chart, 15, 512) ?>;if(!window.Chart)return;var g='#216B45',y='#F2B705';
new Chart(document.getElementById('cAct'),{type:'bar',data:{labels:d.labels,datasets:[{label:'Logins & signups',data:d.logins,backgroundColor:g},{label:'Purchase requests',data:d.orders,backgroundColor:y},{label:'New ads',data:d.ads,backgroundColor:'#4f8cff'}]},options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
new Chart(document.getElementById('cStatus'),{type:'doughnut',data:{labels:['Pending','Live','Reserved','Sold'],datasets:[{data:d.status,backgroundColor:[y,'#2fb585','#4f8cff','#8e5bd9']}]},options:{plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('cCat'),{type:'bar',data:{labels:d.cats.labels,datasets:[{label:'Listed',data:d.cats.listed,backgroundColor:g},{label:'Sold',data:d.cats.sold,backgroundColor:y}]},options:{plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\janwar-mart-laravel\janwar-mart-laravel\resources\views/admin/overview.blade.php ENDPATH**/ ?>