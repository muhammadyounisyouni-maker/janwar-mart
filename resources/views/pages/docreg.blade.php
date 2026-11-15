@extends('layouts.app')
@section('route', 'docreg')
@section('content')
<h2>📝 {{ __('Register as a doctor') }}</h2>
@if($doc && !in_array($doc->status, ['rejected', 'review']))<div class="panel">{{ __('Your registration status') }}: <span class="tag">{{ __('ds_'.$doc->status) }}</span> <a class="btn" href="/doctor/dashboard">{{ __('Doctor dashboard') }}</a></div>
@else
@if($doc)<div class="note">{{ __('Please update your details and documents and send again.') }} @if($doc->note)<b>{{ $doc->note }}</b>@endif</div>@endif
<form class="panel" method="post" action="/doctor/register" enctype="multipart/form-data" style="display:grid;gap:10px">@csrf
  <input name="name" value="{{ old('name', $doc->name ?? auth()->user()->name) }}" placeholder="{{ __('Full name') }} *" required>
  <input name="phone" type="tel" value="{{ old('phone', $doc->phone ?? auth()->user()->phone) }}" placeholder="03XXXXXXXXX *" required>
  <input name="wa" type="tel" value="{{ old('wa', $doc->wa ?? '') }}" placeholder="WhatsApp (03XXXXXXXXX)">
  <input name="email" type="email" value="{{ old('email', $doc->email ?? '') }}" placeholder="{{ __('Email (optional)') }}">
  <input name="qual" value="{{ old('qual', $doc->qual ?? '') }}" placeholder="{{ __('Qualification (e.g. DVM)') }} *" required>
  <input name="uni" value="{{ old('uni', $doc->uni ?? '') }}" placeholder="{{ __('University') }} *" required>
  <input name="reg" value="{{ old('reg', $doc->reg ?? '') }}" placeholder="{{ __('Veterinary council registration no.') }} *" required>
  <input name="exp" type="number" min="0" max="60" value="{{ old('exp', $doc->exp ?? 0) }}" placeholder="{{ __('Years of experience') }} *" required>
  <input name="clinic" value="{{ old('clinic', $doc->clinic ?? '') }}" placeholder="{{ __('Clinic / hospital') }} *" required>
  <select name="city" required><option value="">{{ __('Choose a city') }}</option>@foreach(config('janwar.cities') as $k => $n)<option value="{{ $k }}" @selected(old('city', $doc->city ?? '') === $k)>{{ city_name($k) }}</option>@endforeach</select>
  <input name="addr" value="{{ old('addr', $doc->addr ?? '') }}" placeholder="{{ __('Clinic address') }}">
  <label class="lb">{{ __('Profile photo (optional)') }}</label><input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
  <label class="lb">{{ __('Degree certificate') }} * (JPG/PNG/PDF)</label><input type="file" name="cert" accept="image/jpeg,image/png,image/webp,application/pdf" required>
  <label class="lb">{{ __('Registration / licence document') }} * (JPG/PNG/PDF)</label><input type="file" name="lic" accept="image/jpeg,image/png,image/webp,application/pdf" required>
  <small>🔒 {{ __('Your documents are private: only the admin can open them.') }}</small>
  <label class="pmo"><input type="checkbox" name="consent" value="1" required> {{ __('I allow my name, clinic and phone to be shown publicly after verification') }}</label>
  <button>{{ __('Send for verification') }}</button></form>
@endif
@endsection
