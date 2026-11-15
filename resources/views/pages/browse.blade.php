@extends('layouts.app')
@section('route', 'browse')
@section('title', __('Animals'))
@section('content')
<div class="chips">
  <a class="btn chip {{ !request('cat') || request('cat') === 'all' ? 'on' : '' }}" href="{{ request()->fullUrlWithQuery(['cat' => null, 'page' => null]) }}">{{ __('All') }}</a>
  @foreach($cats as $c)<a class="btn chip {{ request('cat') === $c->key ? 'on' : '' }}" href="{{ request()->fullUrlWithQuery(['cat' => $c->key, 'page' => null]) }}"><i class="ci pic-{{ $c->key }}"></i>{{ $c->label() }}</a>@endforeach
</div>
<div class="shop">
<aside><form class="panel" method="get" action="/animals" style="display:grid;gap:10px">
  <h3 style="margin:0">⚙ {{ __('Filters') }}</h3>
  <input type="hidden" name="cat" value="{{ request('cat') }}">
  <input name="q" value="{{ request('q') }}" placeholder="{{ __('Search animal or city…') }}">
  <select name="city" aria-label="{{ __('City') }}"><option value="all">{{ __('All cities') }}</option>@foreach(config('janwar.cities') as $k => $n)<option value="{{ $k }}" @selected(request('city') === $k)>{{ city_name($k) }}</option>@endforeach</select>
  <div class="row2"><input type="number" name="min" value="{{ request('min') }}" placeholder="{{ __('Min price') }}" min="0"><input type="number" name="max" value="{{ request('max') }}" placeholder="{{ __('Max price') }}" min="0"></div>
  <select name="sort" aria-label="{{ __('Sort') }}"><option value="">{{ __('Newest') }}</option><option value="low" @selected(request('sort') === 'low')>{{ __('Lowest price') }}</option><option value="high" @selected(request('sort') === 'high')>{{ __('Highest price') }}</option></select>
  <button>{{ __('Apply') }}</button><a class="btn ghost" href="/animals">{{ __('Clear') }}</a>
</form></aside>
<section>
  <p><b>{{ $list->total() }}</b> {{ __('animals found') }}</p>
  @if($list->count())<div class="grid">@foreach($list as $l)@include('partials.card')@endforeach</div><div class="pager">{{ $list->links() }}</div>
  @else<div class="panel" style="text-align:center">{{ __('No animals found. Try changing the filters.') }}</div>@endif
</section></div>
@endsection
