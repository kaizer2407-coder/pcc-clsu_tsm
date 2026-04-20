<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0a1f44; }
        .card { border-radius: 15px; }
        .btn-yellow { background-color: #ffc107; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card p-4 shadow" style="width: 350px;">
        <a href="/login" class="btn-close position-absolute top-0 end-0 m-3"></a>
        <h4 class="text-center mb-3">Register</h4>

        <form method="POST" action="/register">
            @csrf
            <div class="mb-2">
                <input type="text" name="name" class="form-control" placeholder="First Name">
            </div>

            <div class="mb-2">
                <input type="text" name="m_name" class="form-control" placeholder="Middle Name">
            </div>

            <div class="mb-2">
                <input type="text" name="l_name" class="form-control" placeholder="Last Name">
            </div>

            <div class="mb-2">
                <input type="email" name="email" class="form-control" placeholder="Email">
            </div>

            <div class="mb-2">
                <input type="password" name="password" class="form-control" placeholder="Password">
            </div>

            <button class="btn btn-yellow w-100">Register</button>
        </form>
    </div>

</body>
</html>