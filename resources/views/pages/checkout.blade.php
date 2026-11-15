@extends('layouts.app')
@section('route', 'cart')
@section('content')
<h2>🧾 {{ __('Your details & payment') }}</h2>
<ol class="stp"><li>1. {{ __('Cart') }}</li><li class="on">2. {{ __('Your details & payment') }}</li><li>3. {{ __('Admin confirmation') }}</li></ol>
@if(!$items->count())<div class="panel">{{ __('Your cart is empty.') }} <a href="/animals">{{ __('Browse animals') }}</a></div>
@else
<form class="panel" method="post" action="/checkout" style="display:grid;gap:10px">@csrf
  <p><b>{{ $items->count() }}</b> {{ __('animals') }} — <b>{{ __('PKR') }} {{ pkr($items->sum('price')) }}</b></p>
  <input name="name" value="{{ old('name', auth()->user()->name) }}" placeholder="{{ __('Your name') }} *" required>
  <input name="phone" type="tel" inputmode="tel" value="{{ old('phone', auth()->user()->phone) }}" placeholder="03XXXXXXXXX *" required>
  <input name="city" value="{{ old('city') }}" placeholder="{{ __('City / area') }}">
  <textarea name="note" rows="2" placeholder="{{ __('Note (optional)') }}">{{ old('note') }}</textarea>
  <label class="pmo"><input type="checkbox" name="admin_present" value="1" @checked(old('admin_present'))> 👤 {{ __('I want the admin to be present at the purchase') }}</label>
  <h3 style="margin:6px 0 0">{{ __('Payment method') }}</h3>
  @php $pm = ['jazz' => ['JazzCash', !empty($acc['jazz']['no'])], 'easy' => ['Easypaisa', !empty($acc['easy']['no'])], 'bank' => [__('Bank transfer'), !empty($acc['bank']['iban'])], 'cod' => [__('Cash on delivery'), !empty($acc['cod'])]]; @endphp
  @foreach($pm as $k => [$label, $on])
    <label class="pmo" style="{{ $on ? '' : 'opacity:.5' }}"><input type="radio" name="pay_method" value="{{ $k }}" @checked(old('pay_method', $k === 'cod' ? 'cod' : null) === $k) @disabled(!$on)> <span><b>{{ $label }}</b>
      @if($k === 'jazz' && $on)<br><small dir="ltr">{{ $acc['jazz']['title'] }} · {{ $acc['jazz']['no'] }}</small>@endif
      @if($k === 'easy' && $on)<br><small dir="ltr">{{ $acc['easy']['title'] }} · {{ $acc['easy']['no'] }}</small>@endif
      @if($k === 'bank' && $on)<br><small dir="ltr">{{ $acc['bank']['bank'] }} · {{ $acc['bank']['title'] }} · {{ $acc['bank']['iban'] }}</small>@endif
      @if(!$on)<br><small>{{ __('Not available') }}</small>@endif</span></label>
  @endforeach
  <input name="pay_tid" value="{{ old('pay_tid') }}" placeholder="{{ __('Transaction ID (after you send the money)') }}">
  <small>{{ __('Cash on delivery needs no transaction ID: you pay when you receive the animal.') }}</small>
  <button>{{ __('Send purchase request') }}</button>
</form>
@endif
@endsection
