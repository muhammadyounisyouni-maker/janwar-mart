@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">Purchase requests</div><div class="table-responsive"><table class="table table-sm mb-0"><tr><th>Ref</th><th>Buyer</th><th>Animals</th><th>Total</th><th>Payment</th><th>Status</th><th>Actions</th></tr>
@foreach($orders as $o)<tr><td>{{ $o->ref }}<br><small>{{ $o->created_at?->format('Y-m-d H:i') }}</small></td>
<td>{{ $o->name }}<br><small>{{ $o->phone }} · {{ $o->city }}</small>@if($o->admin_present)<br><span class="badge text-bg-warning">admin present</span>@endif @if($o->note)<br><small>{{ $o->note }}</small>@endif</td>
<td>@foreach($o->listings as $l){{ $l->ref }} {{ $l->displayTitle() }}<br>@endforeach</td><td>{{ number_format($o->total) }}</td>
<td>{{ $o->pay_method === 'cod' ? 'Cash on delivery' : $o->pay_method }}@if($o->pay_method !== 'cod')<br><small>TID: {{ $o->pay_tid ?: '—' }}</small>@endif<br><span class="badge text-bg-{{ $o->pay_status === 'confirmed' ? 'success' : (in_array($o->pay_status, ['submitted', 'cod']) ? 'warning' : 'danger') }}">{{ $o->pay_status }}</span></td>
<td><span class="badge text-bg-{{ $o->status === 'completed' ? 'success' : ($o->status === 'cancelled' ? 'danger' : 'warning') }}">{{ $o->status }}</span></td>
<td>@if(in_array($o->status, ['requested', 'contacted']))<div class="d-flex flex-wrap gap-1">
@foreach([['contact', 'Contacted', 'outline-secondary'], ['paid', $o->pay_method === 'cod' ? 'Cash received' : 'Payment received', 'outline-success'], $o->pay_method === 'cod' ? null : ['payreject', 'Payment bad', 'outline-danger'], ['complete', 'Complete (sold)', 'jm'], ['cancel', 'Cancel', 'outline-danger']] as $b)@if($b)<form method="post" action="/admin/orders/{{ $o->id }}/{{ $b[0] }}" onsubmit="return confirm('Are you sure?')">@csrf<button class="btn btn-sm btn-{{ $b[2] }}">{{ $b[1] }}</button></form>@endif @endforeach</div>@endif</td></tr>@endforeach</table></div></div>
@endsection
