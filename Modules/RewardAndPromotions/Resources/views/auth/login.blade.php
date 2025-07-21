<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{asset('adminpanel/assets/img/kaiadmin/FLP-Icon.png')}}">
    <title>Rewards and Promotions</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #007bff;
            /* Bootstrap primary blue */
            color: white;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 10px;
            padding: 2rem;
            backdrop-filter: blur(10px);
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.2);
            border: 1px solid #fff;
            color: white;
        }
        .form-control::placeholder {
            color: #e0e0e0;
        }
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border-color: #ffffff;
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-6 col-lg-4">
            <div class="card login-card text-white shadow-lg">
                <div class="card-body">
                    <h3 class="text-center mb-4">Rewards and Promotions</h3>

                    @if ($errors->any())
                    <div class="alert alert-danger text-white bg-danger border-0">
                        @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    <form action="{{ route('reward-promotions.loginpost') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email" name="email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-light text-primary">Login</button>
                        </div>
                    </form>
                    <!-- <p class="text-center mt-3">
          <a href="#" class="text-white text-decoration-underline">Forgot password?</a>
        </p> -->
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
