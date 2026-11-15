@extends('layouts.app')
@section('route', 'home')
@section('content')
@php $bw = explode(' ', __('Janwar Mart')); @endphp
<div class="home">
<section class="land bleed bgimg"><div class="land-in"><div class="lt rv">
  <span class="chip2">🛡 {{ __('Admin-verified ads') }}</span>
  <div class="wm">{{ $bw[0] }} <b>{{ $bw[1] ?? '' }}</b></div>
  <h1>{{ __('Buy or sell your animal') }}</h1><p>{{ __('Livestock trading made easy') }}</p>
  <div class="row"><a class="btn gold" href="/animals">🛒 {{ __('Buy animals') }}</a><a class="btn btn2 goldo" href="/sell">🤝 {{ __('Sell animal') }}</a></div>
  <div class="mini"><span>✔ {{ __('Verified sellers') }}</span><span>📞 {{ __('Call or WhatsApp') }}</span><span>🩺 {{ __('Verified vet doctors') }}</span></div>
</div></div>
<div class="stats rv"><div class="panel"><b>{{ $nActive }}</b>{{ __('Animals for sale') }}</div><div class="panel"><b>{{ $nVerified }}</b>{{ __('Verified ads') }}</div><div class="panel"><b>{{ $nDoctors }}</b>{{ __('Verified doctors') }}</div></div>
</section>
<div class="mq bleed" aria-hidden="true"><div class="mq-in">
  @for($i = 0; $i < 4; $i++)@foreach($cats as $c)<span>{{ $c->icon }} {{ $c->label() }}</span>@endforeach @foreach(config('janwar.cities') as $k => $n)<span>📍 {{ city_name($k) }}</span>@endforeach @endfor
</div></div>
<div class="tr rv" style="margin-top:18px">
  @foreach([['🛡', __('Admin-verified ads')], ['✔', __('Verified sellers')], ['📞', __('Call or WhatsApp')], ['🩺', __('Verified vet doctors')]] as [$i, $x])<div class="panel"><span style="font-size:24px">{{ $i }}</span><br><b>{{ $x }}</b></div>@endforeach
</div>
<div class="sh rv"><h2>{{ __('Shop by animal') }}</h2></div>
<div class="catrow rv">@foreach($cats as $c)<a class="btn cc" href="/animals?cat={{ $c->key }}"><span class="pp"><i class="pop3 pop-{{ $c->key }}"></i></span><b>{{ $c->label() }}</b><small>{{ $c->ads }} {{ __('ads') }}</small></a>@endforeach</div>
<div class="sh rv"><h2>{{ __('Shop by budget') }}</h2></div>
<div class="bud rv"><a class="btn" href="/animals?max=100000" style="background:var(--card);color:var(--g);border:2px solid var(--g)">{{ __('Under 100,000') }}</a><a class="btn" href="/animals?min=100000&max=300000" style="background:var(--card);color:var(--g);border:2px solid var(--g)">100,000 – 300,000</a><a class="btn" href="/animals?min=300000" style="background:var(--card);color:var(--g);border:2px solid var(--g)">{{ __('Above 300,000') }}</a></div>
<div class="sh rv"><h2>{{ __('Recently added') }}</h2><a href="/animals">{{ __('View all') }} ›</a></div>
@if($recent->count())<div class="grid rv">@foreach($recent as $l)@include('partials.card')@endforeach</div>@else<div class="panel" style="text-align:center">{{ __('No animals found yet.') }}</div>@endif
<div class="duo rv"><div class="d1"><span class="big">🤝</span><h3>{{ __('Have an animal to sell?') }}</h3><p>{{ __('Post a free ad in a few minutes.') }}</p><a class="btn w" href="/sell">{{ __('Sell animal') }}</a></div>
<div class="d2"><span class="big">🩺</span><h3>{{ __('Vet doctors') }}</h3><p>{{ __('Ask an admin-verified doctor about your animal.') }}</p><a class="btn w" href="/doctors">{{ __('Go') }}</a></div></div>
@if($verified->count())<div class="sh rv"><h2>✔ {{ __('Verified sellers') }}</h2></div><div class="grid rv">@foreach($verified as $l)@include('partials.card')@endforeach</div>@endif
<div class="sh rv"><h2>{{ __('How it works') }}</h2></div>
<div class="how rv">
  <div class="panel"><h3 style="margin:0 0 8px">{{ __('Buy animals') }}</h3>@foreach([__('Choose an animal'), __('Talk by phone or WhatsApp'), __('Buy the animal with the admin present')] as $i => $s)<p style="margin:6px 0"><span class="num">{{ $i + 1 }}</span>{{ $s }}</p>@endforeach</div>
  <div class="panel"><h3 style="margin:0 0 8px">{{ __('Sell animal') }}</h3>@foreach([__('Post an ad'), __('Add photo and price'), __('Buyers will call you')] as $i => $s)<p style="margin:6px 0"><span class="num">{{ $i + 1 }}</span>{{ $s }}</p>@endforeach</div>
</div>
<div class="note" style="margin-top:16px">{{ __('Inspect the animal carefully and verify ownership, health and vaccination before paying.') }}</div>
</div>
@endsection
