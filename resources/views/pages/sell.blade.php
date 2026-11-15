@extends('layouts.app')
@section('route', 'sell')
@section('content')
<h2>➕ {{ __('Sell an animal') }}</h2>
<p><small>{{ __('Your ad will be visible to everyone after admin approval.') }}</small></p>
<div class="steps"><i></i><i></i><i></i><i></i><i></i><i></i></div>
<form class="panel" method="post" action="/sell" enctype="multipart/form-data" style="display:grid;gap:10px">@csrf
  <section data-step class="stepbox"><h3>1. {{ __('Which animal do you want to sell?') }}</h3>
    <div class="catrow">@foreach($cats as $c)<label class="pmo"><input type="radio" name="category" value="{{ $c->key }}" required @checked(old('category') === $c->key)> {{ $c->icon }} {{ $c->label() }}</label>@endforeach</div>
    <button type="button" data-next>{{ __('Next') }}</button></section>
  <section data-step class="stepbox hidden-step"><h3>2. {{ __('Add a clear photo of your animal.') }}</h3><small>{{ __('Use daylight and show the whole animal. Up to 4 photos, 3 MB each.') }}</small>
    <input id="photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple required><div class="prev" id="photoPrev"></div>
    <div class="row2"><button type="button" class="ghost" data-prev>{{ __('Back') }}</button><button type="button" data-next>{{ __('Next') }}</button></div></section>
  <section data-step class="stepbox hidden-step"><h3>3. {{ __('Enter the price.') }}</h3>
    <input type="number" name="price" value="{{ old('price') }}" min="1" max="100000000" placeholder="{{ __('Price (PKR)') }} *" required>
    <div class="row2"><button type="button" class="ghost" data-prev>{{ __('Back') }}</button><button type="button" data-next>{{ __('Next') }}</button></div></section>
  <section data-step class="stepbox hidden-step"><h3>4. {{ __('Animal details (optional)') }}</h3>
    <input name="title" value="{{ old('title') }}" placeholder="{{ __('Title (optional)') }}" maxlength="120">
    <input name="breed" value="{{ old('breed') }}" placeholder="{{ __('Breed') }}"><input name="age" value="{{ old('age') }}" placeholder="{{ __('Age (years)') }}"><input name="weight" value="{{ old('weight') }}" placeholder="{{ __('Approx. weight (kg)') }}">
    <textarea name="descr" rows="3" placeholder="{{ __('Short description') }}">{{ old('descr') }}</textarea>
    <div class="row2"><button type="button" class="ghost" data-prev>{{ __('Back') }}</button><button type="button" data-next>{{ __('Next') }}</button></div></section>
  <section data-step class="stepbox hidden-step"><h3>5. {{ __('Location and contact') }}</h3>
    <select name="city" required><option value="">{{ __('Choose a city') }}</option>@foreach(config('janwar.cities') as $k => $n)<option value="{{ $k }}" @selected(old('city') === $k)>{{ city_name($k) }}</option>@endforeach</select>
    <input name="contact" type="tel" value="{{ old('contact', auth()->user()->phone) }}" placeholder="03XXXXXXXXX *" required>
    <label class="pmo"><input type="checkbox" name="consent" value="1" required> {{ __('I allow contact by phone and WhatsApp') }}</label>
    <div class="row2"><button type="button" class="ghost" data-prev>{{ __('Back') }}</button><button type="button" data-next>{{ __('Next') }}</button></div></section>
  <section data-step class="stepbox hidden-step"><h3>6. {{ __('Review and publish') }}</h3>
    <p class="note">{{ __('Your plan allows') }} {{ auth()->user()->adLimit() }} {{ __('ads') }}.</p>
    <div class="row2"><button type="button" class="ghost" data-prev>{{ __('Back') }}</button><button type="submit">{{ __('Post in Janwar Mart') }}</button></div></section>
</form>
<noscript><style>.stepbox.hidden-step{display:grid!important}</style></noscript>
@endsection
