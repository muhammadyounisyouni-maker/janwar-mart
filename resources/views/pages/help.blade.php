@extends('layouts.app')
@section('route', 'help')
@section('content')
<h2>🛟 {{ __('Help & safety') }}</h2>
<div class="panel"><h3>{{ __('Trust & safety') }}</h3>
  <p><b>👀 {{ __('See it yourself') }}</b> — {{ __('Inspect the animal, owner and papers in person.') }}</p>
  <p><b>🛡 {{ __('Admin verification') }}</b> — {{ __('The admin checks sellers and gives the ‘Verified’ badge.') }}</p>
  <p class="note">{{ __('Janwar Mart does not guarantee the quality of any animal or the honesty of any seller; always satisfy yourself before paying.') }}</p>
  <p class="note">{{ __('Inspect the animal carefully and verify ownership, health and vaccination before paying.') }}</p>
  <a class="btn" href="/contact">📞 {{ __('Contact admin') }}</a></div>
@endsection
