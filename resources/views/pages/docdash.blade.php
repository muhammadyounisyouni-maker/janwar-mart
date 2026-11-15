@extends('layouts.app')
@section('route', 'docdash')
@section('content')
<h2>🩺 {{ __('Doctor dashboard') }}</h2>
@if(!$doc)<div class="panel">{{ __('You have not registered as a doctor yet.') }} <a class="btn" href="/doctor/register">{{ __('Register as a doctor') }}</a></div>
@else
<div class="panel"><b>{{ $doc->name }}</b> <span dir="ltr">{{ $doc->ref }}</span> <span class="tag">{{ __('ds_'.$doc->status) }}</span>@if($doc->note)<p class="note">{{ $doc->note }}</p>@endif
  @if(in_array($doc->status, ['rejected', 'review']))<a class="btn" href="/doctor/register">{{ __('Update and send again') }}</a>@endif
  @if($doc->histories->count())<details><summary>{{ __('History') }}</summary>@foreach($doc->histories as $h)<p><small>{{ $h->created_at?->format('Y-m-d') }} — {{ __('ds_'.$h->status) }} {{ $h->note }}</small></p>@endforeach</details>@endif</div>
@if($doc->status === 'verified')
<h3>💬 {{ __('Questions from owners') }}</h3>
@forelse($cons as $c)
<div class="panel" style="margin-bottom:10px"><b dir="ltr">{{ $c->ref }}</b> <span class="tag">{{ $c->status === 'new' ? __('New') : __('Answered') }}</span><p><b>{{ $c->animal }}</b></p><p>{{ $c->text }}</p>
  @foreach([$c->photo1, $c->photo2] as $p)@if($p)<img src="{{ asset('storage/'.$p) }}" alt="" style="width:96px;height:96px;object-fit:cover;border-radius:10px">@endif @endforeach
  <p><small>{{ $c->name }} · <span dir="ltr">{{ $c->phone }}</span></small></p>
  @if($c->reply)<div class="note">{{ $c->reply }}</div>@else
  <form method="post" action="/doctor/consultations/{{ $c->id }}/reply" style="display:grid;gap:8px">@csrf<textarea name="reply" rows="3" required minlength="5" placeholder="{{ __('Your answer') }}"></textarea><button>{{ __('Send answer') }}</button></form>@endif</div>
@empty<div class="panel">{{ __('No questions yet.') }}</div>@endforelse
@endif
@endif
@endsection
