<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        Login GHL SMART SCRIPTS
    </title>
    <link rel="stylesheet" href="{{ asset('auth/style.css') }}">
</head>
<body>
    <div class="box">
        <span class="borderLine"></span>
        <form method="POST" action="{{ route('login-post-member') }}">
            @csrf

            <h2>Sign in</h2>
            <div class="inputBox">
                <input type="email" name="email" :value="old('email')" required autocomplete="username">
                <span>Username</span>
                <i></i>
            </div>
            <div class="inputBox">
                <input type="password" name="password" required autocomplete="current-password" >
                <span>Password</span>
                <i></i>
            </div>
            {{-- <div class="links">
                <a href="#">Forgot Password</a>
                <a href="#">Signup</a>
            </div> --}}
            <input type="submit" id="submit" value="Login" style="margin-top: 30px">
        </form>
    </div>

</body>
</html>
