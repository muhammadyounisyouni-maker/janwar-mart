@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header d-flex justify-content-between">Notifications<form method="post" action="/admin/notifications/read">@csrf<button class="btn btn-sm btn-outline-secondary">Mark all read</button></form></div>
<table class="table table-sm mb-0">@forelse($items as $n)<tr><td>{{ $n->icon }}</td><td>@unless($n->is_read)<b>• </b>@endunless{{ $n->text }}</td><td><small>{{ $n->created_at?->format('Y-m-d H:i') }}</small></td></tr>@empty<tr><td>None</td></tr>@endforelse</table></div>
@endsection
