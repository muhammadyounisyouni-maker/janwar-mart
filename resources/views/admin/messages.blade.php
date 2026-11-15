@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">Contact messages</div><div class="table-responsive"><table class="table table-sm mb-0"><tr><th>Time</th><th>Type</th><th>Ad ref</th><th>Message</th><th>From</th><th></th></tr>
@forelse($items as $m)<tr><td>{{ $m->created_at?->format('Y-m-d H:i') }}</td><td>{{ $m->type }}</td><td>{{ $m->ad_ref }}</td><td>{{ $m->message }}</td><td>{{ $m->name }}<br><small>{{ $m->phone }}</small></td>
<td>@if($m->status === 'open')<form method="post" action="/admin/messages/{{ $m->id }}/resolve">@csrf<button class="btn btn-sm btn-jm">Resolved</button></form>@else<span class="badge text-bg-success">resolved</span>@endif</td></tr>@empty<tr><td colspan="6">No messages yet</td></tr>@endforelse</table></div></div>
@endsection
