@extends('layouts.app')
@section('route', 'orders')
@section('content')
<h2>🧾 {{ __('My orders') }}</h2>
@forelse($orders as $o)
<div class="panel" style="margin-bottom:10px">
  <b dir="ltr">{{ $o->ref }}</b> <span class="tag">{{ __('o_'.$o->status) }}</span> <span class="tag">{{ __('p_'.$o->pay_status) }}</span> @if($o->admin_present)<span class="tag">👤 {{ __('Admin present') }}</span>@endif
  <p>@foreach($o->listings as $l)<a href="/animals/{{ $l->ref }}">{{ $l->displayTitle() }}</a> ({{ pkr($l->pivot->price) }})@if(!$loop->last), @endif @endforeach</p>
  <p><b>{{ __('Total') }}: {{ __('PKR') }} {{ pkr($o->total) }}</b> · {{ $o->pay_method === 'cod' ? __('Cash on delivery') : strtoupper($o->pay_method) }}</p>
  @if($o->meeting)<div class="note">📅 {{ __('Meeting') }}: {{ $o->meeting->meet_at }} · {{ $o->meeting->place }} · {{ $o->meeting->admin_name }}</div>@endif
  @if($o->dispute)<div class="note">⚖ {{ __('Dispute') }}: {{ __('d_'.$o->dispute->status) }} @if($o->dispute->resolution)— {{ $o->dispute->resolution }}@endif</div>
  @elseif($o->status === 'completed')
    <details><summary class="btn ghost" style="list-style:none">⚖ {{ __('Problem with this purchase? Ask for a refund') }}</summary>
      <form method="post" action="/orders/{{ $o->id }}/dispute" style="display:grid;gap:8px;margin-top:8px">@csrf<textarea name="reason" rows="3" required minlength="10" placeholder="{{ __('Explain the problem (at least 10 characters)') }}"></textarea><button>{{ __('Send to admin') }}</button></form></details>
  @endif
</div>
@empty<div class="panel" style="text-align:center">{{ __('No orders yet.') }}</div>@endforelse
@endsection
