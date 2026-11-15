@extends('layouts.admin')
@section('content')
@php
$s = $stats; $todo = $s['pending'] + $s['doctors_pending'] + $s['open_orders']; $h = now()->hour;
$k = fn ($i, $n, $t, $url, $c = '') => "<div class='col-6 col-md-4 col-xl-2'><a class='kpi $c' href='$url'><div class='ki'>$i</div><div><b>$n</b><span>$t</span></div></a></div>";
@endphp
<div class="hello p-3 p-md-4 mb-3 d-flex flex-wrap justify-content-between align-items-center gap-3">
  <div><h2 class="h4 mb-1">{{ $h < 12 ? 'Good morning' : ($h < 18 ? 'Good afternoon' : 'Good evening') }}, {{ auth()->user()->name }} 👋</h2>
    <div class="opacity-75">{{ $todo ? "You have $todo item".($todo > 1 ? 's' : '').' waiting for your action.' : 'Everything is up to date. Nice work!' }}</div></div>
  <div class="d-flex gap-2"><a class="btn btn-warning fw-bold" href="/admin/tab/ads">Review ads</a><a class="btn btn-warning fw-bold" href="/admin/tab/orders">Open orders</a></div>
</div>
<div class="row g-2 mb-3">
{!! $k('👥', $s['users'], 'Members', '/admin/tab/users') !!}{!! $k('🛒', $s['buyers'], 'Buyers', '/admin/tab/users') !!}{!! $k('🐄', $s['sellers'], 'Sellers', '/admin/tab/users') !!}{!! $k('🩺', $s['doctors'], 'Doctors', '/admin/tab/doctors') !!}
{!! $k('⏳', $s['pending'], 'Ads waiting approval', '/admin/tab/ads', $s['pending'] ? 'warn' : '') !!}{!! $k('📢', $s['active'], 'Live on homepage', '/admin/tab/ads') !!}
{!! $k('🧾', $s['open_orders'], 'Open purchase requests', '/admin/tab/orders', $s['open_orders'] ? 'warn' : '') !!}{!! $k('✅', $s['sold'], 'Animals sold (bought)', '/admin/tab/sold') !!}{!! $k('💰', 'Rs '.number_format($s['revenue']), 'Sales value', '/admin/tab/sold') !!}
{!! $k('🔑', $s['logins_today'], 'Logins / signups today', '/admin/tab/logins') !!}{!! $k('⚠', $s['failed_today'], 'Failed logins today', '/admin/tab/logins', $s['failed_today'] ? 'bad' : '') !!}{!! $k('💵', $s['cod'], 'Cash-on-delivery orders', '/admin/tab/payments') !!}
</div>
<div class="row g-3 mb-3">
  <div class="col-lg-6"><div class="card h-100"><div class="card-header fw-semibold">Activity — last 7 days</div><div class="card-body"><canvas id="cAct" height="130"></canvas></div></div></div>
  <div class="col-md-6 col-lg-3"><div class="card h-100"><div class="card-header fw-semibold">Ads by status</div><div class="card-body"><canvas id="cStatus" height="170"></canvas></div></div></div>
  <div class="col-md-6 col-lg-3"><div class="card h-100"><div class="card-header fw-semibold">By category</div><div class="card-body"><canvas id="cCat" height="170"></canvas></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">⏳ Ads to review</span><a href="/admin/tab/ads">all ›</a></div><ul class="list-group list-group-flush">
    @forelse($pendingAds as $a)<li class="list-group-item d-flex justify-content-between align-items-center gap-2"><span>@if($a->cover())<img class="thumb me-2" style="width:40px;height:32px" src="{{ $a->cover() }}" alt="">@endif<b>{{ $a->displayTitle() }}</b><br><small class="text-secondary">{{ $a->ref }} · Rs {{ number_format($a->price) }}</small></span>
      <span class="d-flex gap-1"><form method="post" action="/admin/listings/{{ $a->id }}/approve">@csrf<button class="btn btn-sm btn-jm">Approve</button></form><form method="post" action="/admin/listings/{{ $a->id }}/reject">@csrf<button class="btn btn-sm btn-outline-danger">Reject</button></form></span></li>
    @empty<li class="list-group-item text-secondary">Nothing waiting 🎉</li>@endforelse</ul></div></div>
  <div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">🧾 Open requests & 🩺 doctors</span><a href="/admin/tab/orders">all ›</a></div><ul class="list-group list-group-flush">
    @foreach($openOrders as $o)<li class="list-group-item d-flex justify-content-between"><span><b>{{ $o->ref }}</b> <small class="text-secondary">{{ $o->name }} · Rs {{ number_format($o->total) }}</small></span><span class="badge text-bg-{{ $o->pay_status === 'confirmed' ? 'success' : 'warning' }}">{{ $o->pay_status }}</span></li>@endforeach
    @foreach($pendingDocs as $d)<li class="list-group-item d-flex justify-content-between"><span>🩺 <b>{{ $d->name }}</b> <small class="text-secondary">{{ $d->clinic }}</small></span><a class="btn btn-sm btn-outline-secondary" href="/admin/tab/doctors">Verify</a></li>@endforeach
    @if(!$openOrders->count() && !$pendingDocs->count())<li class="list-group-item text-secondary">No open requests</li>@endif</ul></div></div>
  <div class="col-lg-4"><div class="card h-100"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">🔔 Recent activity</span><a href="/admin/tab/notifications">all ›</a></div><ul class="list-group list-group-flush">
    @forelse($feed as $n)<li class="list-group-item">{{ $n->icon }} @unless($n->is_read)<b>• </b>@endunless{{ $n->text }}<br><small class="text-secondary">{{ $n->created_at?->diffForHumans() }}</small></li>@empty<li class="list-group-item text-secondary">No activity yet</li>@endforelse</ul></div></div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function(){var d=@json($chart);if(!window.Chart)return;var g='#216B45',y='#F2B705';
new Chart(document.getElementById('cAct'),{type:'bar',data:{labels:d.labels,datasets:[{label:'Logins & signups',data:d.logins,backgroundColor:g},{label:'Purchase requests',data:d.orders,backgroundColor:y},{label:'New ads',data:d.ads,backgroundColor:'#4f8cff'}]},options:{responsive:true,scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
new Chart(document.getElementById('cStatus'),{type:'doughnut',data:{labels:['Pending','Live','Reserved','Sold'],datasets:[{data:d.status,backgroundColor:[y,'#2fb585','#4f8cff','#8e5bd9']}]},options:{plugins:{legend:{position:'bottom'}}}});
new Chart(document.getElementById('cCat'),{type:'bar',data:{labels:d.cats.labels,datasets:[{label:'Listed',data:d.cats.listed,backgroundColor:g},{label:'Sold',data:d.cats.sold,backgroundColor:y}]},options:{plugins:{legend:{position:'bottom'}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});})();
</script>
@endpush
