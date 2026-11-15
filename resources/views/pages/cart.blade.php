@extends('layouts.app')
@section('route', 'cart')
@section('content')
<h2>🛒 {{ __('Cart') }}</h2>
<ol class="stp"><li class="on">1. {{ __('Cart') }}</li><li>2. {{ __('Your details & payment') }}</li><li>3. {{ __('Admin confirmation') }}</li></ol>
@if($items->count())
  <div class="panel">
  @foreach($items as $l)
    <div class="row3"><span class="ic">@if($l->cover())<img src="{{ $l->cover() }}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:12px">@else<i class="pi pic-{{ $l->category?->key }}"></i>@endif</span>
      <div><b>{{ $l->displayTitle() }}</b><br><small>📍 {{ city_name($l->city) }} · <span dir="ltr">{{ $l->ref }}</span></small></div>
      <b>{{ __('PKR') }} {{ pkr($l->price) }}</b>
      <form method="post" action="/cart/{{ $l->ref }}">@csrf @method('DELETE')<button class="ghost" aria-label="{{ __('Remove') }}">✕</button></form></div>
  @endforeach
  <div class="tot"><span>{{ __('Total') }}</span><b>{{ __('PKR') }} {{ pkr($items->sum('price')) }}</b></div>
  <a class="btn" href="/checkout" style="margin-top:12px;width:100%">{{ __('Continue') }} ›</a></div>
@else<div class="panel" style="text-align:center">{{ __('Your cart is empty.') }}<br><a class="btn" href="/animals" style="margin-top:10px">{{ __('Browse animals') }}</a></div>@endif
@endsection
