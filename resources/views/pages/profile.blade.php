@extends('layouts.app')
@section('route', 'profile')
@section('content')
<h2>⚙ {{ __('Profile') }}</h2>
<div class="panel"><div class="kv"><span>{{ __('Name') }}</span><b>{{ $u->name }}</b><span>{{ __('Phone') }}</span><b dir="ltr">{{ $u->phone }}</b><span>{{ __('Email') }}</span><b>{{ $u->email ?: '—' }}</b><span>{{ __('Role') }}</span><b>{{ __('r_'.$u->role) }}</b><span>{{ __('Plan') }}</span><b>{{ $u->plan }} ({{ $u->adLimit() }} {{ __('ads') }})</b></div>
<div class="acts" style="margin-top:12px"><a class="btn" href="/my-ads">📋 {{ __('My ads') }}</a><a class="btn ghost" href="/orders">🧾 {{ __('My orders') }}</a><a class="btn ghost" href="/plans">💎 {{ __('Plans') }}</a><a class="btn ghost" href="/favorites">♥ {{ __('Favorites') }}</a></div>
<form method="post" action="/logout" style="margin-top:12px">@csrf<button class="ghost">🚪 {{ __('Log out') }}</button></form></div>
@endsection
