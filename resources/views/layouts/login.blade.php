<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Toko Jaya Abadi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #007bff, #6610f2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-group {
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }

        .form-control {
            padding-left: 35px;
        }

        .btn-login {
            background: #007bff;
            border: none;
            font-weight: bold;
            transition: background 0.3s ease-in-out;
        }

        .btn-login:hover {
            background: #0056b3;
        }

        .alert {
            font-size: 14px;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h3 class="text-center mb-3">Login</h3>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('login.process') }}" method="POST">
            @csrf
            <div class="mb-3 form-group">
                <i class="fa fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3 form-group">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-login w-100 py-2">Login</button>
        </form>
    </div>

</body>

</html>
