@include('layouts.includes');

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Login</title>
</head>

<body>
    <div class="bg"></div>
    <div class="bg bg2"></div>
    <div class="bg bg3"></div>
    <div class="login-card">
        <h1 class="login-header mb-4">Bem Vindo!</h1>
        <div class="inner-addon left-addon">
            <label for="email" class="lbls mb-2">Email:</label><br>
            <i class="fa-regular fa-user"></i>
            <input id="email" name="email" type="text" placeholder="Insira o seu Email" class="form-style"
                autocomplete="off">
        </div>

        <div class="inner-addon left-addon">
            <label for="password" class="lbls mt-3 mb-2">Password:</label><br>
            <i class="fa-regular fa-lock-keyhole"></i>
            <input id="password" name="password" type="password" placeholder="Insira a sua Password" class="form-style"
                autocomplete="off">
        </div>

        <span id="forgot_pssw"><a href="#">Esqueceu-se da password?</a></span>
        <button id="login_btn" class="mt-3">Login</button>
        <span id="register"><a href="#" class="mt-2">Criar nova conta</a></span>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
    </div>
</body>

</html>

<script src="{{ asset('js/login.js') }}"></script>
