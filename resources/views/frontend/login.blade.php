@include('layouts.includes');

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        div.loaderFADE {
            opacity: 0.5;
            background: #000;
            width: 100%;
            height: 100%;
            z-index: 10;
            top: 0;
            left: 0;
            position: fixed;
        }

        .loader-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80%;
        }

        .loader2 {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.897);
            border-top-color: transparent;
            animation: rot1 1.2s linear infinite;
        }

        @keyframes rot1 {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <title>Login</title>
</head>

<body>
    <div class="loaderFADE visually-hidden">
        <div class="loader-container" id="lc">
            <div class="loader2"></div>
        </div>
    </div>
    <div class="bg"></div>
    <div class="bg bg2"></div>
    <div class="bg bg3"></div>
    <div class="login-card">
        <h1 class="login-header" id="greeting">Bem Vindo!</h1>
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

        <span id="forgot_pssw"><a href="#">Esqueçeu-se da password?</a></span>
        <button id="login_btn" class="mt-3">Login</button>
        <span id="register"><a href="/register" class="mt-2">Criar nova conta</a></span>
    </div>
</body>

</html>

<script>
    // Performs the login
    function login_action() {
        $.ajax({
                method: "post",
                url: "auth",
                data: {
                    _token: "{{ csrf_token() }}",
                    email: $("#email").val(),
                    password: $("#password").val(),
                },
            })
            .done((res) => {
                window.location.replace(
                    res.isProfessional ? "/professional" : "/home"
                );
            })
            .fail((err) => {
                errorToast(err.responseJSON.status, err.responseJSON.message);
            });
    }

    $(document).ready(() => {
        $("#login_btn").on("click", () => {
            login_action();
        });
        $(document).on("keypress", (e) => {
            if (e.which == 13) login_action();
        });
        // Forgot password
        $("#forgot_pssw").on('click', () => {
            const hasempty = animateErr(["email"]);
            if (hasempty) return;
            $(".loaderFADE").removeClass("visually-hidden");
            $.ajax({
                method: "post",
                url: "/forgotpass",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "email": $("#email").val()
                }
            }).done((res) => {
                $(".loaderFADE").addClass("visually-hidden");
                successToast(res.title, res.message);
            }).fail((err) => {
                $(".loaderFADE").addClass("visually-hidden");
                errorToast(err.responseJSON.title, err.responseJSON.message);
            });
        })
    });
</script>
