@extends('layouts.admin')
@section('content')
<div class="row g-2 mb-3"><div class="col-md-4"><div class="stat"><b>{{ $listed }}</b>Animals came for selling</div></div><div class="col-md-4"><div class="stat"><b>{{ $sold->count() }}</b>Animals bought (sold)</div></div><div class="col-md-4"><div class="stat"><b>Rs {{ number_format($sold->sum('sold_price')) }}</b>Total value</div></div></div>
<div class="card"><div class="card-header">Sold animals — who sold, who bought</div><div class="table-responsive"><table class="table table-sm mb-0"><tr><th>Date</th><th>Ref</th><th>Animal</th><th>Seller</th><th>Buyer</th><th>Price</th></tr>
@foreach($sold as $l)<tr><td>{{ $l->sold_at?->format('Y-m-d') }}</td><td>{{ $l->ref }}</td><td>{{ $l->displayTitle() }}</td><td>{{ $l->owner?->name }}<br><small>{{ $l->contact }}</small></td><td>{{ $l->buyer_name }}<br><small>{{ $l->buyer_phone }}</small></td><td>{{ number_format($l->sold_price) }}</td></tr>@endforeach</table></div></div>
@endsection
