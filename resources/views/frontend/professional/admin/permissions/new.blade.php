@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'perms'])

<link rel="stylesheet" href="{{ asset('css/perms.css') }}">

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-3 g-0">
                <div class="new-card">
                    <h4 style="font-weight: 600;">Nome:</h4>
                    <input type="text" class="form-control" placeholder="Nome do Tipo" autocomplete="off" id="name">
                    <button class="btn btn-primary form-control mt-3" id="create">Criar</button>
                </div>
            </div>
            <div class="col-6">
                <div class="checkboxes-card">
                    <h4 style="font-weight: 600">Permissões:</h4>
                    <hr class="mt-2">
                    @php
                        $map = [['label' => 'Ver Pedidos', 'id' => 'view_orders'], ['label' => 'Editar Pedidos', 'id' => 'write_orders'], ['label' => 'Ver Menu', 'id' => 'view_menu'], ['label' => 'Editar Menu', 'id' => 'write_menu'], ['label' => 'Ver Estatisticas', 'id' => 'view_stats'], ['label' => 'Convidar Utilizadores', 'id' => 'invite_users'], ['label' => 'Banir Utilizadores', 'id' => 'ban_users'], ['label' => 'Admin', 'id' => 'admin']];
                    @endphp
                    <div class="row">
                        @foreach ($map as $permission)
                            <div class="col-12">
                                <label class="perm-label">{{ $permission['label'] }}</label>
                                <div class="form-check form-switch perm-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="{{ $permission['id'] }}">
                                </div>
                                @if ($permission['label'] == 'Admin')
                                    <br>
                                    <span class="text-muted">Se ativar esta opção este tipo de utilizador tera todas as
                                        permissões</span>
                                @endif
                            </div>
                            <hr class="mt-3">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

<script>
    $(document).ready(() => {
        // which checkboxes were checked by user checking the admin so that they can be unchecked when unchecking the admin checkbox
        let toUncheck = [];

        const map = [
            "view_orders",
            "write_orders",
            "view_menu",
            "write_menu",
            "view_stats",
            "invite_users",
            "ban_users",
            "admin",
        ]

        // To iterate through the checkboxes and either see if they are checked or check/uncheck them
        function iterateBoxes(get, toCheck = true) {
            if (get) var permissions = {};
            $.each((toCheck ? map : toUncheck), (key, val) => {
                if (get) {
                    permissions[val] = $("#" + val).is(":checked");
                    return true;
                }
                if (!toCheck) {
                    $("#" + val).prop("checked", false)
                    toUncheck = [];
                } else {
                    if (!$("#" + val).is(":checked")) {
                        $("#" + val).prop("checked", true)
                        toUncheck[toUncheck.length] = val;
                    };
                }
            })
            if (get) return permissions;
        }

        $("#admin").on('click', () => {
            console.log(toUncheck)
            if (!$("#admin").is(":checked")) {
                iterateBoxes(false, false);
                return;
            }

            $.each(map, (key, val) => {
                iterateBoxes(false);
            });
        })

        $("#create").on('click', () => {
            var permissions = iterateBoxes(true);

            hasEmpty = animateErr(["name"]);
            if (hasEmpty) return;

            $.ajax({
                method: "post",
                url: "/professional/admin/permissions/save_types",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "name": $("#name").val(),
                    "permissions": permissions
                }
            }).done((res) => {
                if (res.title == "Erro") {
                    errorToast(res.title, res.message);
                } else {
                    successToast(res.title, res.message);
                    setTimeout(() => {
                        window.location.replace("/professional/admin/permissions");
                    }, 2000);
                }
            })
        })
    })
</script>
