@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">Ad reports</div><table class="table table-sm mb-0"><tr><th>Time</th><th>Ad</th><th>Reason</th><th></th></tr>
@forelse($items as $r)<tr><td>{{ $r->created_at?->format('Y-m-d H:i') }}</td><td>{{ $r->listing?->ref }} {{ $r->listing?->displayTitle() }}</td><td>{{ $r->reason }}</td>
<td>@if($r->status === 'open')<form method="post" action="/admin/reports/{{ $r->id }}/resolve">@csrf<button class="btn btn-sm btn-jm">Resolved</button></form>@else<span class="badge text-bg-success">resolved</span>@endif</td></tr>@empty<tr><td colspan="4">No reports yet</td></tr>@endforelse</table></div>
@endsection
