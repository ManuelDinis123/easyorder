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
        $("#create").on('click', () => {
            var map = [
                "view_orders",
                "write_orders",
                "view_menu",
                "write_menu",
                "view_stats",
                "invite_users",
                "ban_users",
                "admin",
            ]

            var permissions = {};
            $.each(map, (key, val) => {
                permissions[val] = $("#" + val).is(":checked");
            })

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
                }
            })
        })
    })
</script>
