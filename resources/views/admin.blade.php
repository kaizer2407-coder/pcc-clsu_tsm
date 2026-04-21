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

/* MOBILE FIX */
@media (max-width: 768px) {
    table { font-size: 11px; }
    th, td { padding: 5px; white-space: nowrap; }
    .btn-sm { padding: 3px 6px; font-size: 10px; }
    .form-control-sm, .form-select-sm {
        font-size: 10px;
        padding: 2px;
    }
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
<div class="row g-3">
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

<!-- REQUEST TABLE -->
<div class="card mt-4 shadow">
<div class="card-header bg-navy text-white">All Travel Requests</div>

<div class="card-body">
<div class="table-responsive">
<table class="table table-hover">

<thead>
<tr>
<th>Passenger</th>
<th>Destination</th>
<th class="d-none d-md-table-cell">Purpose</th>
<th>Date</th>
<th class="d-none d-md-table-cell">Requester</th>
<th>Driver</th>
<th class="d-none d-md-table-cell">Ticket</th>
<th>Status</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@forelse($requests as $req)
<tr>
<td>{{ $req->passenger }}</td>
<td>{{ $req->destination }}</td>
<td class="d-none d-md-table-cell">{{ $req->purpose }}</td>
<td>{{ $req->date }}</td>
<td class="d-none d-md-table-cell">{{ $req->user->name ?? '' }}</td>

<td>
<select name="driver" form="approveForm{{ $req->id }}" class="form-select form-select-sm">
<option value="">Select</option>
@foreach($drivers as $driver)
<option value="{{ $driver->id }}">{{ $driver->name }}</option>
@endforeach
</select>
</td>

<td class="d-none d-md-table-cell">
<input type="text" class="form-control form-control-sm" value="{{ $req->tickets }}">
</td>

<td>
<span class="badge 
@if($req->status == 'Approved') bg-success
@elseif($req->status == 'Pending') bg-warning text-dark
@else bg-danger
@endif">
{{ $req->status }}
</span>
</td>

<td>
<div class="d-flex gap-1 flex-wrap">
<form id="approveForm{{ $req->id }}" method="POST" action="/request/{{ $req->id }}/approve">
@csrf
<button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check"></i></button>
</form>

<a href="/request/{{ $req->id }}/reject" class="btn btn-warning btn-sm">
<i class="bi bi-x"></i>
</a>

<form method="POST" action="/request/{{ $req->id }}">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
</form>
</div>
</td>

</tr>
@empty
<tr>
<td colspan="9" class="text-center">No data found</td>
</tr>
@endforelse
</tbody>

</table>
</div>

{{ $requests->links() }}

</div>
</div>

<!-- DRIVER TABLE -->
<div class="card mt-4 shadow">

<div class="card-header bg-navy text-white d-flex justify-content-between">
<span>Drivers</span>

<button type="button" class="btn btn-warning btn-sm"
        data-bs-toggle="modal"
        data-bs-target="#addDriverModal">
+ Add Driver
</button>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table table-hover">

<thead>
<tr>
<th>Name</th>
<th>License</th>
<th>Status</th>
</tr>
</thead>

<tbody>
@forelse($drivers as $driver)
<tr>
<td>{{ $driver->name }}</td>
<td>{{ $driver->license_no }}</td>
<td><span class="badge bg-success">Available</span></td>
</tr>
@empty
<tr>
<td colspan="3" class="text-center">No drivers</td>
</tr>
@endforelse
</tbody>

</table>
</div>
</div>
</div>

</div>

<!-- ✅ ADD DRIVER MODAL (FIXED) -->
<div class="modal fade" id="addDriverModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="POST" action="/driver">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Add Driver</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="text" name="name" class="form-control mb-2" placeholder="Driver Name" required>
          <input type="text" name="license_no" class="form-control" placeholder="License No">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
        </div>

      </form>

    </div>
  </div>
</div>

<!-- ✅ ONLY ONE JS (IMPORTANT) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>