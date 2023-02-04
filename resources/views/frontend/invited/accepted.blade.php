@include('layouts.includes')
<link rel="stylesheet" href="{{ asset('css/invites/accepted.css') }}">

@if (isset($hasRestaurant))
    <div class="context">
        <div class="all">
            <div class="invite-card">
                <h4 class="title">Convite Para</h4>
                <div class="logo-contain">
                    <img class="logo" src="{{ $logo_name != null ? asset('img/logos/' . $logo_name) : $logo_url }}">
                </div>
                <h1 class="title mt-3">{{ $r_name }}</h1>
                <hr>
                <span style="display: flex; justify-content: center;">Não pode estar associado a mais que um
                    restaurante!</span>
            </div>
        </div>
    </div>
@else
    <div class="context">
        <div class="all">
            <div class="invite-card">
                <h4 class="title">Convite Para</h4>
                <div class="logo-contain">
                    <img class="logo" src="{{ $logo_name != null ? asset('img/logos/' . $logo_name) : $logo_url }}">
                </div>
                <h1 class="title mt-3">{{ $r_name }}</h1>
                <hr>
                @if (isset($userID))
                    <div class="row g-0 mt-5 pb-5">
                        <div class="col-3">
                            <img src="{{ asset('img/pfp/' . $pfp) }}" class="pfp mt-3">
                        </div>
                        <div class="col-9">
                            <label class="username">{{ $username }}</label><br />
                            <input type="password" class="form-control" placeholder="Insira a sua password"
                                style="width: 70%" id="psw">
                            <button class="btn btn-primary mt-2" id="enter_with_account">Entrar</button>
                        </div>
                    </div>
                @else
                    <label class="lbls">Nome:</label>
                    <input type="text" id="first" class="form-control mt-1" placeholder="Primeiro">
                    <input type="text" id="last" class="form-control mt-3" placeholder="Ultimo">

                    <label class="lbls mt-4">Data de nascimento:</label>
                    <input type="text" id="db" class="form-control mt-1" data-provide="datepicker">

                    <label class="lbls mt-4">Email:</label>
                    <input type="text" id="email" value="{{ $email }}"
                        class="form-control mt-1 text-muted" placeholder="O seu email" autocomplete="off" disabled>

                    <label class="lbls mt-4">Password:</label>
                    <input type="password" id="password" class="form-control mt-1" placeholder="A sua palavra-passe">

                    <button id="reg" class="btn btn-primary mt-5 form-control">Registar</button>
                @endif
            </div>
        </div>
    </div>
@endif


<div class="area">
    <ul class="circles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>

<script>
    $("#db").datepicker({
        format: 'dd/mm/yyyy'
    })

    $(document).ready(() => {
        $("#reg").on('click', () => {
            var map = ["first", "last", "db", "email", "password"];
            var hasEmpty = animateErr(map);

            if (hasEmpty) return;

            var data = {};
            $.each(map, (key, id) => {
                data[id] = $("#" + id).val();
            })

            data['_token'] = "{{ csrf_token() }}";
            data['type'] = "{{ $type }}"
            data['restaurant_id'] = "{{ $r_id }}";
            data['is_create'] = true;
            data['inv_uid'] = "{{ $token }}";

            $.ajax({
                method: 'post',
                url: '/invite/register',
                data: data
            }).done((res) => {
                successToast(res.title, res.message);
                window.location.replace("/");
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        })

        $("#enter_with_account").on('click', () => {
            var map = ["psw"];
            var hasEmpty = animateErr(map);

            if (hasEmpty) return;

            $.ajax({
                method: 'post',
                url: '/invite/register',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "userID": {{ (isset($userID)) ? $userID : 0  }},
                    "password": $("#psw").val(),
                    "has_account": true,
                    'restaurant_id': "{{ $r_id }}",
                    'type': "{{ $type }}",
                    'inv_uid': "{{ $token }}"
                }
            }).done((res) => {
                successToast(res.title, res.message);
                window.location.replace("/");
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        })
    });
</script>
