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

.label {
    font-weight: 600;
    color: #0a1f44;
    font-size: 13px;
}

.value {
    font-size: 13px;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar bg-navy navbar-dark px-3">
    <span class="navbar-brand text-yellow">Admin Panel</span>
    <a href="/login" class="btn btn-warning btn-sm">Logout</a>
</nav>

<div class="container mt-4">

<h3 class="mb-4">Admin Dashboard</h3>

<!-- CARDS -->
<div class="row g-3 mb-3">
    <div class="col-12 col-md-4">
        <div class="card p-3 shadow text-center">
            <h6>Total Requests</h6>
            <h3>{{ $total ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card p-3 shadow text-center">
            <h6>Approved</h6>
            <h3>{{ $approved ?? 0 }}</h3>
        </div>
    </div>

    <div class="col-12 col-md-4">
        <div class="card p-3 shadow text-center">
            <h6>Pending</h6>
            <h3>{{ $pending ?? 0 }}</h3>
        </div>
    </div>
</div>

<!-- REQUEST LIST -->
<div class="row g-3">

@forelse($requests as $req)

<div class="col-12">
<div class="card shadow p-3">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center mb-2">
    <strong>{{ $req->passenger }}</strong>

    <span class="badge 
    @if($req->status == 'Approved') bg-success
    @elseif($req->status == 'Pending') bg-warning text-dark
    @else bg-danger
    @endif">
    {{ $req->status }}
    </span>
</div>

<!-- DETAILS -->
<div class="row mb-2">
    <div class="col-6">
        <div class="label">Destination</div>
        <div class="value">{{ $req->destination }}</div>
    </div>

    <div class="col-6">
        <div class="label">Date</div>
        <div class="value">{{ $req->date }}</div>
    </div>

    <div class="col-6">
        <div class="label">Purpose</div>
        <div class="value">{{ $req->purpose }}</div>
    </div>

    <div class="col-6">
        <div class="label">Requester</div>
        <div class="value">{{ $req->user->name ?? '' }}</div>
    </div>
</div>

<!-- DRIVER -->
<div class="mb-2">
<select name="driver" form="approveForm{{ $req->id }}" class="form-select form-select-sm">
<option value="">Select Driver</option>

@foreach($drivers as $driver)
<option value="{{ $driver->id }}" {{ $req->driver == $driver->id ? 'selected' : '' }}>
{{ $driver->name }} - {{ $driver->license_no }}
</option>
@endforeach

</select>
</div>

<!-- TICKET -->
<div class="mb-2">
<form method="POST" action="/request/{{ $req->id }}/tickets">
@csrf
@method('PUT')

<div class="d-flex">
<input type="text"
       name="tickets"
       class="form-control form-control-sm me-2"
       value="{{ $req->tickets }}"
       placeholder="Ticket No"
       {{ $req->tickets ? 'readonly' : '' }}>

@if(!$req->tickets)
<button class="btn btn-success btn-sm">Save</button>
@endif
</div>

</form>
</div>

<!-- REMARKS -->
<div class="mb-2">
<form method="POST" action="/request/{{ $req->id }}/remarks">
@csrf
@method('PUT')

<div class="d-flex">
<input type="text"
       name="admin_remarks"
       class="form-control form-control-sm me-2"
       value="{{ $req->admin_remarks }}"
       placeholder="Remarks">

<button class="btn btn-primary btn-sm">Save</button>
</div>

</form>
</div>

<!-- ACTIONS -->
<div class="d-flex flex-wrap gap-2 mt-2">

<form id="approveForm{{ $req->id }}" method="POST" action="/request/{{ $req->id }}/approve">
@csrf
<button class="btn btn-success btn-sm">
<i class="bi bi-check"></i>
</button>
</form>

<form method="POST" action="/request/{{ $req->id }}/reject">
@csrf
<button class="btn btn-warning btn-sm">
<i class="bi bi-x"></i>
</button>
</form>

<form method="POST" action="/request/{{ $req->id }}/reset-ticket">
@csrf
@method('PUT')
<button class="btn btn-secondary btn-sm">
<i class="bi bi-arrow-counterclockwise"></i>
</button>
</form>

<form method="POST" action="/request/{{ $req->id }}">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm">
<i class="bi bi-trash"></i>
</button>
</form>

</div>

</div>
</div>

@empty
<div class="col-12 text-center">No data found</div>
@endforelse

</div>

<!-- PAGINATION -->
<div class="mt-3">
{{ $requests->links() }}
</div>

<!-- DRIVER SECTION -->
<div class="card mt-4 shadow">

<div class="card-header bg-navy text-white d-flex justify-content-between">
<span>Drivers</span>

<button class="btn btn-warning btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#addDriverModal">
+ Add Driver
</button>
</div>

<div class="card-body">

<div class="row g-2">

@forelse($drivers as $driver)

<div class="col-12 col-md-4">
<div class="card p-2 shadow-sm">

<strong>{{ $driver->name }}</strong>
<div>{{ $driver->license_no }}</div>

<span class="badge bg-success mt-1">Available</span>

</div>
</div>

@empty
<div class="text-center">No drivers</div>
@endforelse

</div>

</div>
</div>

</div>

<!-- MODAL -->
<div class="modal fade" id="addDriverModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" action="/driver">
        @csrf

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

      </form>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>