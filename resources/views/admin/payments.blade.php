@extends('layouts.admin')
@section('content')
<div class="card mb-3"><div class="card-header">Payment accounts shown to buyers (a method with an empty account cannot be chosen)</div><div class="card-body">
<form method="post" action="/admin/payments" class="row g-2">@csrf
  <div class="col-md-6"><label class="form-label">JazzCash — account title</label><input class="form-control" name="jazz_title" value="{{ $acc['jazz']['title'] ?? '' }}"></div><div class="col-md-6"><label class="form-label">JazzCash — number</label><input class="form-control" name="jazz_no" value="{{ $acc['jazz']['no'] ?? '' }}"></div>
  <div class="col-md-6"><label class="form-label">Easypaisa — account title</label><input class="form-control" name="easy_title" value="{{ $acc['easy']['title'] ?? '' }}"></div><div class="col-md-6"><label class="form-label">Easypaisa — number</label><input class="form-control" name="easy_no" value="{{ $acc['easy']['no'] ?? '' }}"></div>
  <div class="col-md-4"><label class="form-label">Bank name</label><input class="form-control" name="bank_name" value="{{ $acc['bank']['bank'] ?? '' }}"></div><div class="col-md-4"><label class="form-label">Bank — account title</label><input class="form-control" name="bank_title" value="{{ $acc['bank']['title'] ?? '' }}"></div><div class="col-md-4"><label class="form-label">IBAN</label><input class="form-control" name="bank_iban" value="{{ $acc['bank']['iban'] ?? '' }}"></div>
  <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" name="cod" value="1" id="cod" @checked(!empty($acc['cod']))><label class="form-check-label" for="cod">Cash on delivery enabled</label></div></div>
  <div class="col-12"><button class="btn btn-jm">Save</button></div></form></div></div>
<div class="card"><div class="card-header">Latest orders and payment state</div><table class="table table-sm mb-0"><tr><th>Ref</th><th>Method</th><th>TID</th><th>Pay status</th><th>Total</th></tr>@foreach($orders as $o)<tr><td>{{ $o->ref }}</td><td>{{ $o->pay_method }}</td><td>{{ $o->pay_tid }}</td><td>{{ $o->pay_status }}</td><td>{{ number_format($o->total) }}</td></tr>@endforeach</table></div>
@endsection
