@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">Consultations</div><div class="table-responsive"><table class="table table-sm mb-0"><tr><th>Ref</th><th>Doctor</th><th>Animal</th><th>Question</th><th>From</th><th>Status</th><th>Answer</th></tr>
@foreach($cons as $c)<tr><td>{{ $c->ref }}<br><small>{{ $c->created_at?->format('Y-m-d') }}</small></td><td>{{ $c->doctor?->name }}</td><td>{{ $c->animal }}</td><td>{{ $c->text }}</td><td>{{ $c->name }}<br><small>{{ $c->phone }}</small></td><td><span class="badge text-bg-{{ $c->status === 'new' ? 'warning' : 'success' }}">{{ $c->status }}</span></td><td>{{ $c->reply }}</td></tr>@endforeach</table></div></div>
@endsection
