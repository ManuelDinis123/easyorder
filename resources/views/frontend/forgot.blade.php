@include('layouts.includes')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <title>Repor Password</title>
</head>

<body>
    <div class="bg"></div>
    <div class="bg bg2"></div>
    <div class="bg bg3"></div>
    <div class="login-card" style="width: 490px">
        <h1 class="login-header" id="greeting">Nova Password!</h1>

        <label class="lbls">Nova Password:</label>
        <input type="password" id="psw" class="form-control" placeholder="*****">

        <button class="btn btn-primary form-control mt-4" id="newPsw">Submeter</button>
    </div>
</body>

</html>


<script>
    $(document).ready(() => {
        $("#newPsw").on('click', () => {
            animateErr(["psw"]);
            $.ajax({
                method: "post",
                url: "/savenewpass",
                data: {
                    _token: "{{ csrf_token() }}",
                    password: $("#psw").val(),
                    token: "{!! $token !!}",
                }
            }).done((res) => {
                window.location.href = "/";
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        });
    })
</script>
