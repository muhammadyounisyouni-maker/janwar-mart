@extends('layouts.admin')
@section('content')
@foreach([['⏳ Waiting for approval', ['pending']], ['✅ Live / reserved / sold', ['active', 'reserved', 'sold']], ['Rejected', ['rejected']]] as [$title, $st])
<div class="card mb-3"><div class="card-header">{{ $title }}</div><div class="table-responsive"><table class="table table-sm mb-0"><tr><th></th><th>Ref</th><th>Animal</th><th>Seller</th><th>Price</th><th>City</th><th>Status</th><th>Actions</th></tr>
@foreach($ads->whereIn('status', $st) as $a)<tr><td>@if($a->cover())<a href="{{ $a->cover() }}" target="_blank"><img class="thumb" src="{{ $a->cover() }}" alt=""></a>@endif</td><td>{{ $a->ref }}</td>
<td>{{ $a->displayTitle() }}<br><small>{{ $a->category?->name_en }} {{ $a->breed }} {{ $a->age ? $a->age.' yr' : '' }} {{ $a->weight ? $a->weight.' kg' : '' }}</small></td><td>{{ $a->owner?->name }}<br><small>{{ $a->contact }}</small></td><td>{{ number_format($a->price) }}</td><td>{{ city_name($a->city) }}</td>
<td><span class="badge text-bg-secondary">{{ $a->status }}</span> @if($a->verified)<span class="badge text-bg-success">verified</span>@endif</td>
<td><div class="d-flex flex-wrap gap-1">
@foreach(array_filter([in_array($a->status, ['pending', 'rejected']) ? ['approve', 'Approve + Verify', 'jm'] : null, $a->status === 'pending' ? ['reject', 'Reject', 'outline-danger'] : null, $a->status === 'active' ? [$a->verified ? 'unverify' : 'verify', $a->verified ? 'Remove badge' : 'Give badge', 'outline-secondary'] : null, $a->status === 'active' ? ['hide', 'Hide', 'outline-secondary'] : null, ['remove', 'Remove', 'outline-danger']]) as [$act, $lbl, $cls])
<form method="post" action="/admin/listings/{{ $a->id }}/{{ $act }}">@csrf<button class="btn btn-sm btn-{{ $cls }}">{{ $lbl }}</button></form>@endforeach</div></td></tr>@endforeach</table></div></div>
@endforeach
@endsection
