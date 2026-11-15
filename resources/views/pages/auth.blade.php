@extends('layouts.app')
@section('route', 'auth')
@section('content')
<h2>🔐 {{ __('Account') }}</h2>
<div class="row2"><a class="btn {{ $tab === 'signup' ? '' : 'ghost' }}" href="/register">{{ __('Sign up') }}</a><a class="btn {{ $tab === 'login' ? '' : 'ghost' }}" href="/login">{{ __('Log in') }}</a></div>
@if($tab === 'signup')
<form class="panel" method="post" action="/register" style="display:grid;gap:10px;margin-top:12px">@csrf
  <input name="name" value="{{ old('name') }}" placeholder="{{ __('Your name') }} *" required>
  <input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" placeholder="{{ __('Phone') }} (03XXXXXXXXX) *" required>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px"><input name="otp" inputmode="numeric" maxlength="6" autocomplete="one-time-code" placeholder="{{ __('Verification code (6 digits)') }} *" required><button type="button" class="ghost" data-otp="signup" data-phone="#phone" data-sent="{{ __('Code sent by SMS') }}">{{ __('Send code') }}</button></div>
  <small id="otpmsg" role="status"></small>
  <input name="email" type="email" value="{{ old('email') }}" placeholder="{{ __('Email (optional)') }}">
  <input name="password" type="password" autocomplete="new-password" minlength="6" placeholder="{{ __('Password (at least 6 characters)') }} *" required>
  <div class="roles"><b>{{ __('Who are you?') }} *</b>
    @foreach(['buyer' => '🛒 '.__('I want to buy animals'), 'seller' => '🐄 '.__('I want to sell animals'), 'doctor' => '🩺 '.__('I am a vet doctor')] as $v => $t)<label class="pmo"><input type="radio" name="role" value="{{ $v }}" @checked(old('role', 'buyer') === $v)> {{ $t }}</label>@endforeach</div>
  <label class="pmo"><input type="checkbox" name="enable_2fa" value="1" @checked(old('enable_2fa'))> 🔐 {{ __('Protect my account with Google Authenticator (two-factor login)') }}</label>
  <button>{{ __('Sign up') }}</button><small>{{ __('Your signup and login are recorded for safety (admin can see them).') }}</small>
</form>
@elseif($tab === 'reset')
<form class="panel" method="post" action="/password/reset" style="display:grid;gap:10px;margin-top:12px">@csrf
  <h3 style="margin:0">🔑 {{ __('Reset password') }}</h3>
  <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="{{ __('Phone') }} (03XXXXXXXXX) *" required>
  <div style="display:grid;grid-template-columns:2fr 1fr;gap:8px"><input name="otp" inputmode="numeric" maxlength="6" placeholder="{{ __('Verification code (6 digits)') }}" required><button type="button" class="ghost" data-otp="reset" data-phone="#phone" data-sent="{{ __('If this phone is registered, a code was sent') }}">{{ __('Send code') }}</button></div>
  <small id="otpmsg" role="status"></small>
  <input name="password" type="password" minlength="6" autocomplete="new-password" placeholder="{{ __('New password') }} *" required>
  <button>{{ __('Reset password') }}</button>
</form>
@else
<form class="panel" method="post" action="/login" style="display:grid;gap:10px;margin-top:12px">@csrf
  <input name="login" value="{{ old('login') }}" placeholder="{{ __('Phone or email') }}" required>
  <input name="password" type="password" autocomplete="current-password" placeholder="{{ __('Password') }}" required>
  <label class="pmo"><input type="checkbox" name="remember" value="1"> {{ __('Keep me logged in') }}</label>
  <button>{{ __('Log in') }}</button><a class="btn ghost" href="/password/reset">{{ __('Forgot password?') }}</a>
  <small>🔐 {{ __('If you turned on two-factor login, you will be asked for the code from Google Authenticator next.') }}</small>
</form>
@endif
@endsection