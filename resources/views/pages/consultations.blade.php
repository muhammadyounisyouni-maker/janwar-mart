@extends('layouts.app')
@section('route', 'consultations')
@section('content')
<h2>💬 {{ __('My consultations') }}</h2>
@forelse($cons as $c)
<div class="panel" style="margin-bottom:10px"><b dir="ltr">{{ $c->ref }}</b> · {{ $c->doctor?->name }} <span class="tag">{{ $c->status === 'new' ? __('Waiting for answer') : __('Answered') }}</span><p>{{ $c->text }}</p>@if($c->reply)<div class="note"><b>{{ __('Doctor’s answer') }}:</b> {{ $c->reply }}</div>@endif</div>
@empty<div class="panel" style="text-align:center">{{ __('No consultations yet.') }} <a href="/doctors">{{ __('Vet doctors') }}</a></div>@endforelse
@endsection
