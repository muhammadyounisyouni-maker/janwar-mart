@extends('layouts.app')
@section('route', 'favs')
@section('content')
<h2>♥ {{ __('Favorites') }}</h2>
@if($list->count())<div class="grid">@foreach($list as $l)@include('partials.card')@endforeach</div>@else<div class="panel" style="text-align:center">{{ __('No favorites yet. Tap the heart on an animal to save it.') }}</div>@endif
@endsection
