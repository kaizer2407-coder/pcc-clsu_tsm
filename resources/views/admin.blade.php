<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background-color: #f4f6f9; }
.bg-navy { background-color: #0a1f44; }
.text-yellow { color: #ffc107; }
.card { border-radius: 12px; }
</style>
</head>

<body>

<!-- ================= NAVBAR ================= -->
<nav class="navbar bg-navy navbar-dark px-3">
    <span class="navbar-brand text-yellow">Admin Panel</span>
    <a href="/login" class="btn btn-warning btn-sm">Logout</a>
</nav>

<!-- ================= MAIN CONTAINER ================= -->
<div class="container mt-4">

<!-- ================= ALERTS ================= -->
@if(session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- ================= TITLE ================= -->
<h3 class="mb-4">Admin Dashboard</h3>

<!-- ================= CARDS ================= -->
<div class="row g-4">

    <div class="col-md-4">
        <div class="card p-3 shadow">
            <h6>Total Requests</h6>
            <h3>{{ $total ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow">
            <h6>Approved</h6>
            <h3>{{ $approved ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow">
            <h6>Pending</h6>
            <h3>{{ $pending ?? 0 }}</h3>
        </div>
    </div>

</div>

<!-- ================= REQUEST TABLE ================= -->
<div class="card mt-4 shadow">

<div class="card-header bg-navy text-white">
All Travel Requests
</div>

<div class="card-body">

<table class="table table-hover">

<thead>
<tr>
<th>Passenger</th>
<th>Destination</th>
<th>Purpose</th>
<th>Date</th>
<th>Requester</th>
<th>Driver</th>
<th>Ticket</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@forelse($requests as $req)

<tr>

<td>{{ $req->passenger }}</td>
<td>{{ $req->destination }}</td>
<td>{{ $req->purpose }}</td>
<td>{{ $req->date }}</td>
<td>{{ $req->user->name ?? '' }} {{ $req->user->l_name ?? '' }}</td>

<!-- ================= DRIVER SELECT (NO SAVE BUTTON) ================= -->
<td>
<select name="driver"
        form="approveForm{{ $req->id }}"
        class="form-select form-select-sm">

<option value="">Select Driver</option>

@foreach($drivers as $driver)

@php
$isBusy = \App\Models\RequestModel::where('driver', $driver->id)
    ->where('date', $req->date)
    ->where('status', 'Approved')
    ->exists();
@endphp

<option value="{{ $driver->id }}"
    {{ $req->driver == $driver->id ? 'selected' : '' }}
    {{ $isBusy ? 'disabled' : '' }}>
    {{ $driver->name }} - {{ $driver->license_no }}
</option>

@endforeach

</select>
</td>

<!-- ================= TICKET ================= -->
<td>
<form method="POST" action="/request/{{ $req->id }}/tickets">
@csrf

<div class="d-flex">
<input type="text"
       name="tickets"
       class="form-control form-control-sm me-2"
       value="{{ $req->tickets }}"
       placeholder="e.g. 2026-128"
       {{ $req->tickets ? 'readonly' : '' }}
       required>

@if(!$req->tickets)
<button class="btn btn-success btn-sm">Save</button>
@endif

</div>

</form>
</td>

<!-- ================= STATUS ================= -->
<td>
<span class="badge 
@if($req->status == 'Approved') bg-success
@elseif($req->status == 'Pending') bg-warning text-dark
@else bg-danger
@endif">
{{ $req->status }}
</span>
</td>

<!-- ================= ACTION BUTTONS ================= -->
<td class="d-flex gap-2">

<!-- APPROVE (WITH DRIVER) -->
<form id="approveForm{{ $req->id }}" method="POST" action="/request/{{ $req->id }}/approve">
@csrf
<button class="btn btn-success btn-sm" title="Approve">
<i class="bi bi-check-lg"></i>
</button>
</form>

<!-- REJECT -->
<a href="/request/{{ $req->id }}/reject" class="btn btn-warning btn-sm">
<i class="bi bi-x-lg"></i>
</a>

<!-- CLEAR -->
<a href="/request/{{ $req->id }}/clear" class="btn btn-secondary btn-sm">
<i class="bi bi-arrow-counterclockwise"></i>
</a>

<!-- DELETE -->
<form method="POST" action="/request/{{ $req->id }}">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm">
<i class="bi bi-trash"></i>
</button>
</form>

</td>

</tr>

@empty

<tr>
<td colspan="9" class="text-center">No data found</td>
</tr>

@endforelse

</tbody>
</table>

<div class="mt-3">
{{ $requests->links() }}
</div>

</div>
</div>

<!-- ================= DRIVER TABLE ================= -->
<div class="card mt-4 shadow">

<div class="card-header bg-navy text-white d-flex justify-content-between">
<span>Drivers</span>

<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addDriverModal">
+ Add Driver
</button>
</div>

<div class="card-body">

<table class="table table-hover">

<thead>
<tr>
<th>Name</th>
<th>License</th>
<th>Status (Today)</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@forelse($drivers as $driver)

<tr>
<td>{{ $driver->name }}</td>
<td>{{ $driver->license_no }}</td>

<td>
@php
$isBusy = \App\Models\RequestModel::where('driver', $driver->id)
    ->where('date', date('Y-m-d'))
    ->where('status', 'Approved')
    ->exists();
@endphp

<span class="badge {{ $isBusy ? 'bg-danger' : 'bg-success' }}">
{{ $isBusy ? 'On' : 'Available' }}
</span>
</td>

<td>
<button class="btn btn-primary btn-sm"
data-bs-toggle="modal"
data-bs-target="#editDriver{{ $driver->id }}">
Update
</button>
</td>
</tr>

<!-- ================= EDIT DRIVER MODAL ================= -->
<div class="modal fade" id="editDriver{{ $driver->id }}">
<div class="modal-dialog">
<form method="POST" action="/driver/{{ $driver->id }}">
@csrf
@method('PUT')

<div class="modal-content">

<div class="modal-header">
<h5>Edit Driver</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="text" name="name" class="form-control mb-2" value="{{ $driver->name }}" required>
<input type="text" name="license_no" class="form-control" value="{{ $driver->license_no }}">
</div>

<div class="modal-footer">
<button class="btn btn-success">Update</button>
</div>

</div>
</form>
</div>
</div>

@empty

<tr>
<td colspan="4" class="text-center">No drivers</td>
</tr>

@endforelse

</tbody>
</table>

</div>
</div>

</div>

<!-- ================= ADD DRIVER MODAL ================= -->
<div class="modal fade" id="addDriverModal">
<div class="modal-dialog">

<form method="POST" action="/driver">
@csrf

<div class="modal-content">

<div class="modal-header">
<h5>Add Driver</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="text" name="name" class="form-control mb-2" placeholder="Driver Name" required>
<input type="text" name="license_no" class="form-control" placeholder="License No">
</div>

<div class="modal-footer">
<button class="btn btn-success">Save</button>
</div>

</div>

</form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>