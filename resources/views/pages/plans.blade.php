@extends('layouts.app')
@section('route', 'plans')
@section('content')
<h2>💎 {{ __('Plans') }}</h2>
<div class="plans">
  <div class="plan"><h3>Basic</h3><div class="pp2">{{ __('Free') }}</div><ul><li>✔ {{ config('janwar.plans.basic') }} {{ __('ads') }}</li><li>✔ {{ __('Admin verification') }}</li></ul><button class="ghost" disabled>{{ __('Current basic plan') }}</button></div>
  @foreach(['silver' => 10, 'gold' => 999] as $k => $n)
  <div class="plan {{ $k === 'gold' ? 'hot' : '' }}"><h3>{{ ucfirst($k) }}</h3><div class="pp2">Rs {{ pkr(config('janwar.plan_prices.'.$k)) }}<small>/{{ __('month') }}</small></div>
    <ul><li>✔ {{ $k === 'gold' ? __('Unlimited') : $n }} {{ __('ads') }}</li><li>✔ {{ __('Priority support') }}</li></ul>
    @auth<form method="post" action="/profile/plan">@csrf<input type="hidden" name="plan" value="{{ $k }}"><button @disabled(auth()->user()->plan === $k)>{{ auth()->user()->plan === $k ? __('Your plan') : __('Request this plan') }}</button></form>
    @else<a class="btn" href="/login">{{ __('Log in to request') }}</a>@endauth</div>
  @endforeach
</div><p class="note" style="margin-top:12px">{{ __('After you request a plan, the admin contacts you and activates it.') }}</p>
@endsection
