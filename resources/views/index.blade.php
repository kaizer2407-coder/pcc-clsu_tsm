<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Travel Monitoring Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color: #f4f6f9; }
.bg-navy { background-color: #0a1f44 !important; }
.text-yellow { color: #ffc107 !important; }
.card-custom {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-navy navbar-dark shadow">
<div class="container-fluid">
<a class="navbar-brand fw-bold text-yellow">Travel Monitoring System</a>
<a href="/login" class="btn btn-warning btn-sm">Login</a>
</div>
</nav>

<!-- MAIN -->
<div class="container mt-4">

<h3 class="mb-4">Dashboard</h3>

<!-- CARDS -->
<div class="row g-4">

<div class="col-md-4">
<div class="card card-custom p-3">
<h6 class="text-muted">Active Travel</h6>
<h2 class="text-success">{{ $active ?? 0 }}</h2>
</div>
</div>

<div class="col-md-4">
<div class="card card-custom p-3">
<h6 class="text-muted">Pending Approvals</h6>
<h2 class="text-warning">{{ $pending ?? 0 }}</h2>
</div>
</div>

<div class="col-md-4">
<div class="card card-custom p-3">
<h6 class="text-muted">Available Driver</h6>
<h2 class="text-danger">{{ $availableDrivers ?? 0 }}</h2>
</div>
</div>

</div>

<!-- SECOND ROW -->
<div class="row g-4 mt-1">

<div class="col-md-6">
<div class="card card-custom p-3">
<h6 class="text-muted">Total Travelers</h6>
<h2 class="text-primary">{{ $totalPassengers ?? 0 }}</h2>
</div>
</div>

<div class="col-md-6">
<div class="card card-custom p-3">
<h6 class="text-muted">Completed Trips</h6>
<h2 class="text-dark">{{ $completed ?? 0 }}</h2>
</div>
</div>

</div>

<!-- ALL TRAVELS -->
<div class="card card-custom mt-4">

<div class="card-header bg-navy text-white">
All Travels
</div>

<div class="card-body">

<!-- 🔍 SEARCH -->
<form method="GET" action="/" class="mb-3 d-flex justify-content-end">
    <input type="text" name="search"
           class="form-control form-control-sm me-2"
           style="width: 220px;"
           placeholder="Search..."
           value="{{ request('search') }}">

    <button class="btn btn-primary btn-sm">
        🔍
    </button>
</form>

<table class="table table-hover">

<thead>
<tr>
<th>Driver</th>
<th>Passenger</th>
<th>Destination</th>
<th>Status</th>
<th>Date</th>
</tr>
</thead>

<tbody>

@forelse($requests as $req)

@php
$driver = \App\Models\Driver::find($req->driver);
@endphp

<tr>

<td>
{{ $driver ? $driver->name . ' - ' . $driver->license_no : 'Not Assigned' }}
</td>

<td>{{ $req->passenger }}</td>

<td>{{ $req->destination }}</td>

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
{{ \Carbon\Carbon::parse($req->date)->format('F d, Y') }}
</td>

</tr>

@empty

<tr>
<td colspan="5" class="text-center">No data found</td>
</tr>

@endforelse

</tbody>
</table>

<!-- 📄 PAGINATION -->
<div class="mt-3">
{{ $requests->links() }}
</div>

</div>
</div>

<!-- AVAILABLE DRIVERS -->
<div class="card card-custom mt-4">

<div class="card-header bg-navy text-white">
Drivers Status (Today)
</div>

<div class="card-body">

<table class="table table-hover">

<thead>
<tr>
<th>Driver Name</th>
<th>License</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@forelse($drivers as $driver)

@php
$isBusy = \App\Models\RequestModel::where('driver', $driver->id)
    ->where('status', 'Approved')
    ->whereDate('date', date('Y-m-d'))
    ->exists();
@endphp

<tr>
<td>{{ $driver->name }}</td>
<td>{{ $driver->license_no }}</td>

<td>
<span class="badge {{ $isBusy ? 'bg-danger' : 'bg-success' }}">
{{ $isBusy ? 'Busy' : 'Available' }}
</span>
</td>
</tr>

@empty
<tr>
<td colspan="3" class="text-center">No drivers found</td>
</tr>
@endforelse

</tbody>

</table>

</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>