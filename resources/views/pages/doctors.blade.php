@extends('layouts.app')
@section('route', 'doctors')
@section('content')
<h2>🩺 {{ __('Vet doctors') }}</h2>
<p class="note">🩺 {{ __('Only doctors whose documents were checked by the admin are listed here.') }}</p>
<form class="bar" method="get"><input name="q" value="{{ request('q') }}" placeholder="{{ __('Search doctor or clinic') }}"><select name="city"><option value="all">{{ __('All cities') }}</option>@foreach(config('janwar.cities') as $k => $n)<option value="{{ $k }}" @selected(request('city') === $k)>{{ city_name($k) }}</option>@endforeach</select><button>{{ __('Search') }}</button></form>
<div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr))">
@forelse($docs as $d)
<div class="panel"><div class="row3" style="border:0">@if($d->photo)<img class="av" src="{{ asset('storage/'.$d->photo) }}" alt="">@else<span class="av">{{ mb_substr($d->name, 0, 1) }}</span>@endif<div><b>{{ $d->name }}</b><br><small>{{ $d->qual }} · {{ $d->exp }} {{ __('yr') }}</small></div></div>
  <p>🏥 {{ $d->clinic }}<br>📍 {{ city_name($d->city) }} @if($d->addr)— {{ $d->addr }}@endif</p><span class="tag">✔ {{ __('Verified by admin') }}</span>
  <div class="acts" style="margin-top:8px"><a class="btn" href="tel:+{{ preg_replace('/^0/', '92', $d->phone) }}">📞 {{ __('Call') }}</a>@if($d->wa)<a class="btn wa" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/^0/', '92', $d->wa) }}">💬 WhatsApp</a>@endif</div>
  <a class="btn ghost" style="margin-top:8px;width:100%" href="/doctors/{{ $d->id }}/consult">💬 {{ __('Ask a question') }}</a></div>
@empty<div class="panel" style="text-align:center;grid-column:1/-1">{{ __('No verified doctors yet.') }}</div>@endforelse
</div>
@endsection
