@extends('layouts.admin')
@section('content')
<div class="card"><div class="card-header">All members ({{ $users->count() }})</div><div class="table-responsive"><table class="table table-sm mb-0">
<tr><th>Name</th><th>Phone / Email</th><th>Role</th><th>Plan</th><th>Joined</th><th>Last login</th><th>Logins</th><th>Status</th><th></th></tr>
@foreach($users as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->phone }}<br><small>{{ $u->email }}</small></td><td>{{ $u->role }}</td>
<td>@unless($u->isAdmin())<form method="post" action="/admin/users/{{ $u->id }}/plan">@csrf<select name="plan" class="form-select form-select-sm" onchange="this.form.submit()">@foreach(['basic','silver','gold'] as $p)<option @selected($u->plan === $p)>{{ $p }}</option>@endforeach</select></form>@endunless</td>
<td>{{ $u->created_at?->format('Y-m-d H:i') }}</td><td>{{ $u->last_login_at?->format('Y-m-d H:i') ?? '—' }}</td><td>{{ $u->login_count }}</td>
<td><span class="badge text-bg-{{ $u->status === 'active' ? 'success' : 'danger' }}">{{ $u->status }}</span></td>
<td>@unless($u->isAdmin())<form method="post" action="/admin/users/{{ $u->id }}/{{ $u->isBlocked() ? 'unblock' : 'block' }}" onsubmit="return confirm('Are you sure?')">@csrf<button class="btn btn-sm btn-{{ $u->isBlocked() ? 'jm' : 'outline-danger' }}">{{ $u->isBlocked() ? 'Unblock' : 'Block' }}</button></form>@endunless</td></tr>@endforeach
</table></div></div>
@endsection
