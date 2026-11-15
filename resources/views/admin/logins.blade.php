@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">Login / signup activity (latest 300)</div><div class="table-responsive"><table class="table table-sm mb-0"><tr><th>Time</th><th>Who</th><th>Event</th><th>IP</th><th>Device</th></tr>
@foreach($events as $e)<tr><td>{{ $e->created_at?->format('Y-m-d H:i:s') }}</td><td>{{ $e->user?->name }} <small>{{ $e->who }}</small></td><td><span class="badge text-bg-{{ str_contains($e->event, 'failed') ? 'danger' : 'success' }}">{{ $e->event }}</span></td><td>{{ $e->ip }}</td><td><small>{{ $e->ua }}</small></td></tr>@endforeach</table></div></div>
@endsection
