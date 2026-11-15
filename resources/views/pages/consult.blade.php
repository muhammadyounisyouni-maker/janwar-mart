@extends('layouts.app')
@section('route', 'consult')
@section('content')
<h2>💬 {{ __('Ask a question') }} — {{ $doc->name }}</h2>
<form class="panel" method="post" action="/doctors/{{ $doc->id }}/consult" enctype="multipart/form-data" style="display:grid;gap:10px">@csrf
  <input name="animal" value="{{ old('animal') }}" placeholder="{{ __('Which animal? (e.g. cow, 3 years)') }}">
  <textarea name="text" rows="4" required minlength="10" maxlength="2000" placeholder="{{ __('Describe the problem (at least 10 characters)') }} *">{{ old('text') }}</textarea>
  <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple><small>{{ __('Up to 2 photos') }}</small>
  <input name="name" value="{{ old('name', auth()->user()->name) }}" placeholder="{{ __('Your name') }} *" required>
  <input name="phone" type="tel" value="{{ old('phone', auth()->user()->phone) }}" placeholder="03XXXXXXXXX *" required>
  <button>{{ __('Send question') }}</button></form>
@endsection
