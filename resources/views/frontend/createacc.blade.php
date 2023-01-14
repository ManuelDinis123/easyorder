@include('layouts.includes')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <title>Register</title>
</head>

@component('components.modal_builder',
    [
        'modal_id' => 'ageConfirmation',
        'hasBody' => true,
        'rawBody' => "
    <div class='row'>
        <div class='col-4'>
            <i class='fa-solid fa-face-thinking' style='font-size: 150px;'></i>
        </div>
        <div class='col-8'>
            <span style='font-size: 25px; margin-top: 30%'>Tem <span id='totalAge' class='fw-bolder' style='font-size:50px; text-decoration:underline'></span> anos?</span>
        </div>
    </div>    
    ",
        'hasFooter' => true,
        'buttons' => [
            ['label' => 'Sim', 'id' => 'yes', 'class' => 'btn btn-primary'],
            ['label' => 'Não', 'id' => 'no', 'class' => 'btn btn-danger', 'dismiss' => true],
        ],
    ])
@endcomponent

<body>
    <div class="bg"></div>
    <div class="bg bg2"></div>
    <div class="bg bg3"></div>
    <div class="login-card">
        <h1 class="login-header" id="greeting">Bem Vindo!</h1>

        <label class="lbls">Nome:</label>
        <input type="text" id="first" class="form-control" placeholder="Primeiro">
        <input type="text" id="last" class="form-control mt-3" placeholder="Ultimo">

        <label class="lbls mt-3">Data de nascimento:</label>
        <input type="text" id="db" class="form-control" data-provide="datepicker">

        <label class="lbls mt-3">Email:</label>
        <input type="text" id="email" class="form-control" placeholder="O seu email" autocomplete="off">

        <label class="lbls mt-2">Password:</label>
        <input type="password" id="password" class="form-control" placeholder="A sua palavra-passe">

        <button id="reg" class="mt-5">Registar</button>
        <span id="login"><a href="/" class="mt-2">Já tenho uma conta</a></span>

        {{-- <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}"> --}}
    </div>
</body>

</html>


<script>
    function makeAcc() {
        // map of input ids
        var map = ["first", "last", "db", "email", "password"];
        var empty = animateErr(map);

        if (!empty) {
            $.ajax({
                method: "post",
                url: "/createaccount",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "first": $("#first").val(),
                    "last": $("#last").val(),
                    "db": $("#db").val(),
                    "email": $("#email").val(),
                    "password": $("#password").val()
                }
            }).done((res) => {
                if (res.title == "Erro") {
                    errorToast(res.title, res.message);
                    animateErr([res.input], false)
                } else {
                    window.location.replace(res.redirect);
                }
            });
        }
    }

    $("#reg").on('click', () => {
        // calculate age
        dob = new Date($("#db").val());
        var today = new Date();
        var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));

        // Validate the age
        if (age >= 100 || age <= 5 && age >= 0) {
            $("#totalAge").text(age);
            $("#ageConfirmation").modal("toggle");
        } else if (age < 0) {
            errorToast("Erro", "Idade negativa");
            return;
        } else {
            makeAcc();
        }

    })

    $("#yes").on('click', () => {
        makeAcc();
        $("#ageConfirmation").modal("toggle");
    })
</script>
