<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .bg-navy { background-color: #0a1f44; }
        .text-yellow { color: #ffc107; }
        .card { border-radius: 12px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar bg-navy navbar-dark px-3">
    <span class="navbar-brand text-yellow">
        Welcome, {{ Auth::user()->name }}
    </span>

    <a href="/login" class="btn btn-warning btn-sm">Logout</a>
</nav>

<div class="container mt-4">

    <h3 class="mb-4">User Dashboard</h3>

    <!-- Cards -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h6>Approved Trips</h6>
                <h3>{{ $approved }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h6>Pending Trips</h6>
                <h3>{{ $pending }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h6>Cancel Trips</h6>
                <h3>{{ $cancel }}</h3>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card mt-4 shadow">
        <div class="card-header bg-navy text-white d-flex justify-content-between align-items-center">
            <span>My Travel Records</span>

            <!-- Offcanvas Button -->
            <button class="btn btn-warning btn-sm" data-bs-toggle="offcanvas" data-bs-target="#requestCanvas">
                + Request
            </button>
        </div>

        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name of Requester</th>
                        <th>Passenger</th>
                        <th>Destination</th>
                        <th>Purpose</th>
                        <th>Date</th>
                        <th>Driver</th>
                        <th>Ticket No.</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $req)
                    <tr>
                        <td>{{ Auth::user()->name }} {{ Auth::user()->l_name }}</td>
                        <td>{{ $req->passenger }}</td>
                        <td>{{ $req->destination }}</td>
                        <td>{{ $req->purpose }}</td>
                        <td>{{ $req->date }}</td>

                        <!-- DRIVER -->
                        <td>
                          @php
                            $driver = \App\Models\Driver::find($req->driver);
                        @endphp

                        {{ $driver ? $driver->name . ' - ' . $driver->license_no : 'Not Assigned' }}
                        </td>

                        <!-- TICKET -->
                        <td>{{ $req->tickets ?? 'N/A' }}</td>

                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ $req->status }}
                            </span>
                        </td>

                        <td>
                            <form action="/request/{{ $req->id }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
             <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $requests->links() }}
                </div>
        </div>
    </div>

</div>

<!-- OFFCANVAS -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="requestCanvas" style="width: 400px;">
    <div class="offcanvas-header bg-navy text-white">
        <h5 class="offcanvas-title">New Travel Request</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <form method="POST" action="/request">
            @csrf

            <!-- Passenger (auto display only) -->
            <div class="mb-3">
                <label>Passenger</label>
                <input type="text" class="form-control" name="passenger" required>
            </div>

            <div class="mb-3">
                <label>Destination</label>
                <input type="text" class="form-control" name="destination" required>
            </div>

            <div class="mb-3">
                <label>Purpose</label>
                <textarea class="form-control" name="purpose" required></textarea>
            </div>

            <div class="mb-3">
                <label>Date</label>
                <input type="date" class="form-control" name="date" required>
            </div>

            <button type="submit" class="btn btn-warning w-100">
                Submit Request
            </button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>