@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">Admin log (every admin action)</div><table class="table table-sm mb-0">@foreach($items as $a)<tr><td>{{ $a->created_at?->format('Y-m-d H:i:s') }}</td><td>admin #{{ $a->admin_id }}</td><td>{{ $a->action }}</td></tr>@endforeach</table></div>
@endsection
