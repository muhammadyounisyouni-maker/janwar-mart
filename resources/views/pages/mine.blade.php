@extends('layouts.app')
@section('route', 'mine')
@section('content')
<h2>📋 {{ __('My ads') }}</h2>
<p><span class="tag">{{ __('Plan') }}: {{ auth()->user()->plan }}</span> <a href="/plans">{{ __('Upgrade') }} ›</a></p>
@forelse($list as $l)
<div class="panel" style="margin-bottom:10px"><div class="row3"><span class="ic">@if($l->cover())<img src="{{ $l->cover() }}" alt="" style="width:64px;height:64px;object-fit:cover;border-radius:12px">@endif</span>
  <div><b>{{ $l->displayTitle() }}</b> <span dir="ltr">{{ $l->ref }}</span><br><small>{{ __('PKR') }} {{ pkr($l->price) }} · 📍 {{ city_name($l->city) }}</small><br><span class="tag">{{ __('s_'.$l->status) }}</span> @if($l->verified)<span class="tag">✔ {{ __('Verified') }}</span>@endif</div>
  @if(in_array($l->status, ['pending', 'active', 'rejected']))<form method="post" action="/my-ads/{{ $l->ref }}" onsubmit="return confirm('{{ __('Remove this ad?') }}')">@csrf @method('DELETE')<button class="ghost">🗑 {{ __('Remove') }}</button></form>@endif</div></div>
@empty<div class="panel" style="text-align:center">{{ __('You have no ads yet.') }} <a class="btn" href="/sell" style="margin-top:8px">{{ __('Sell animal') }}</a></div>@endforelse
@endsection
