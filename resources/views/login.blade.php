<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0a1f44; }
        .card { border-radius: 15px; }
        .btn-yellow { background-color: #ffc107; }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center vh-100">

    <!-- Back Button -->
    

    <!-- Login Card -->
  <div class="card p-4 shadow position-relative" style="width: 350px;">

        <!-- Close (X) Button -->
        <a href="/index" class="btn-close position-absolute top-0 end-0 m-3"></a>

        <h4 class="text-center mb-3">Login</h4>

        <form method="POST" action="/login">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" name="email">
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" class="form-control" name="password">
            </div>

            <button type="submit" class="btn btn-yellow w-100 mb-2">Login</button>
            <div class="text-center">
                <a href="/register" class="text-decoration-none">
                    Create an account
                </a>
            </div>
        </form>

    </div>

</body>
</html>