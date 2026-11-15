@extends('layouts.app')
@section('route', 'detail')
@section('title', $l->displayTitle())
@section('content')
<div class="panel">
  <h2>{{ $l->displayTitle() }}</h2>
  @if($l->status !== 'active')<p class="tag">{{ __('Status') }}: {{ $l->status }}</p>@endif
  <div class="ph" style="border-radius:16px">@if($l->cover())<img id="mainImg" class="dtl-img" src="{{ $l->cover() }}" alt="">@else<i class="pop3 pop-{{ $l->category?->key }}"></i>@endif</div>
  @if($l->images->count() > 1)<div class="thumbs">@foreach($l->images as $i)<img src="{{ asset('storage/'.$i->path) }}" alt="">@endforeach</div>@endif
  <p class="price">{{ __('PKR') }} {{ pkr($l->price) }}</p>
  <div class="kv"><span>{{ __('Animal') }}</span><b>{{ $l->category?->label() }}</b><span>{{ __('City') }}</span><b>{{ city_name($l->city) }}</b>
    @if($l->breed)<span>{{ __('Breed') }}</span><b>{{ $l->breed }}</b>@endif @if($l->age)<span>{{ __('Age (years)') }}</span><b>{{ $l->age }}</b>@endif
    @if($l->weight)<span>{{ __('Weight (kg)') }}</span><b>{{ $l->weight }}</b>@endif <span>{{ __('Reference no.') }}</span><b dir="ltr">{{ $l->ref }}</b>
    <span>{{ __('Seller') }}</span><b>{{ $l->owner?->name }}</b></div>
  @if($l->descr)<p>{{ $l->descr }}</p>@endif
  <p class="tag">{{ $l->verified ? '✔ '.__('Verified') : '⚠ '.__('Not verified') }}</p>
  <div class="sec"><h3>{{ __('Trust & safety') }}</h3>
    <p><b>👀 {{ __('See it yourself') }}</b> — {{ __('Inspect the animal, owner and papers in person.') }}</p>
    <p><b>🛡 {{ __('Admin verification') }}</b> — {{ $l->verified ? __('The admin has checked this seller.') : __('The admin has not checked this seller yet.') }}</p>
    <small>{{ __('Best: use both. Inspect in person and also look at the admin’s verification.') }}</small></div>
  @php $num = preg_replace('/^0/', '92', $l->contact); @endphp
  <div class="acts" style="margin-top:12px"><a class="btn" href="tel:+{{ $num }}">📞 {{ __('Call') }}</a><a class="btn wa" target="_blank" rel="noopener" href="https://wa.me/{{ $num }}?text={{ rawurlencode(($l->displayTitle()).' '.$l->ref) }}">💬 WhatsApp</a></div>
  @if($l->status === 'active' && !$mine)
  <div class="acts" style="margin-top:8px">
    <form method="post" action="/cart/{{ $l->ref }}" style="display:contents">@csrf<button>🛒 {{ __('Add to cart') }}</button></form>
    <form method="post" action="/buy/{{ $l->ref }}" style="display:contents">@csrf<button class="btn2">⚡ {{ __('Buy now') }}</button></form>
  </div>
  @auth<form method="post" action="/favorites/{{ $l->ref }}" style="margin-top:8px">@csrf<button class="ghost">{{ $fav ? '♥ '.__('Remove from favorites') : '♡ '.__('Save to favorites') }}</button></form>@endauth
  @endif
  <details style="margin-top:14px"><summary class="btn ghost" style="list-style:none">🚩 {{ __('Report this ad') }}</summary>
    <form method="post" action="/animals/{{ $l->ref }}/report" style="display:grid;gap:8px;margin-top:8px">@csrf
      <select name="reason"><option>{{ __('Fake or fraud') }}</option><option>{{ __('Wrong price or details') }}</option><option>{{ __('Animal already sold') }}</option><option>{{ __('Other') }}</option></select><button>{{ __('Send report') }}</button></form></details>
</div>
@endsection
