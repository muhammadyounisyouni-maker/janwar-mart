<article class="card" style="position:relative">
  @auth
    <form class="fav-form" method="post" action="/favorites/{{ $l->ref }}">@csrf<button class="fav" aria-label="{{ __('Favorite') }}">{{ in_array($l->id, $favIds ?? []) ? '♥' : '♡' }}</button></form>
  @else
    <a class="fav-form btn fav" href="/login" aria-label="{{ __('Favorite') }}" style="text-decoration:none">♡</a>
  @endauth
  <a class="card-link" href="/animals/{{ $l->ref }}"><div class="ph" aria-hidden="true">@if($l->cover())<img src="{{ $l->cover() }}" alt="" loading="lazy">@else<i class="pop3 pop-{{ $l->category?->key }}"></i>@endif</div></a>
  <div class="cb">
    <b>{{ $l->displayTitle() }}</b>
    <span class="pr">{{ __('PKR') }} {{ pkr($l->price) }}</span>
    <small>📍 {{ city_name($l->city) }} @if($l->age)· {{ $l->age }} {{ __('yr') }}@endif @if($l->weight)· {{ $l->weight }} kg @endif</small>
    <span class="tag">{{ $l->verified ? '✔ '.__('Verified') : '⚠ '.__('Not verified') }}</span>
    <small>{{ $l->approved_at?->format('Y-m-d') }}</small>
    <div class="acts3">
      <a class="btn ghost full" href="/animals/{{ $l->ref }}">{{ __('View details') }}</a>
      <form method="post" action="/cart/{{ $l->ref }}" style="display:contents">@csrf<button aria-label="{{ __('Add to cart') }}">🛒 +</button></form>
      <form method="post" action="/buy/{{ $l->ref }}" style="display:contents">@csrf<button class="btn2">⚡ {{ __('Buy now') }}</button></form>
    </div>
  </div>
</article>
