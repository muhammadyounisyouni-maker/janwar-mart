@extends('layouts.app')
@section('route', 'contact')
@section('content')
@php $num = config('janwar.contact_number'); @endphp
<h2>📞 {{ __('Contact admin') }}</h2>
<div class="panel"><p>{{ __('Problem with an ad, a seller or your account? Contact the admin.') }}</p>
  <div class="acts"><a class="btn" href="tel:+{{ $num }}">📞 {{ __('Call') }}</a><a class="btn wa" target="_blank" rel="noopener" href="https://wa.me/{{ $num }}">💬 WhatsApp</a></div></div>
<form class="panel" method="post" action="/contact" style="margin-top:12px;display:grid;gap:10px">@csrf
  <select name="type">@foreach(['ad_issue' => __('Problem with an ad'), 'seller_complaint' => __('Complaint about a seller'), 'account_login' => __('Account / login problem'), 'other' => __('Other')] as $v => $t)<option value="{{ $v }}" @selected(old('type') === $v)>{{ $t }}</option>@endforeach</select>
  <input name="ad_ref" value="{{ old('ad_ref') }}" placeholder="{{ __('Ad reference (optional), e.g. JM-1101') }}">
  <textarea name="message" rows="4" required maxlength="1000" placeholder="{{ __('Your message') }} *">{{ old('message') }}</textarea>
  <input name="name" value="{{ old('name', auth()->user()->name ?? '') }}" placeholder="{{ __('Your name') }}">
  <input name="phone" type="tel" value="{{ old('phone', auth()->user()->phone ?? '') }}" placeholder="03XXXXXXXXX *" required>
  <button>{{ __('Send to admin') }}</button></form>
@endsection
