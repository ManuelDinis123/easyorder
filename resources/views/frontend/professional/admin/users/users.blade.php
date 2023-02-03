@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'edit_users'])

<link rel="stylesheet" href="{{ asset('css/admin_users.css') }}">

@component('components.modal_builder', [
    'modal_id' => 'inviteModal',
    'hasHeader' => true,
    'rawHeader' =>
        '<h5 class="modal-title" id="addModalLabel"><i class="fa-regular fa-envelope text-icon"></i> Convidar Utilizadores</h5>',
    'hasBody' => true,
    'inputs' => [
        ['label' => 'Email:', 'type' => 'text', 'id' => 'invite_email', 'placeholder' => 'Email de quem quer convidar'],
    ],
    'select' => [
        'configs' => [
            'id' => 'types',
            'label' => 'Tipo de user:',
            'default' => 'Selecione um tipo',
        ],
        'options' => $types,
    ],
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
        ['label' => 'Confirmar', 'id' => 'confirm', 'class' => 'btn btn-primary'],
    ],
])
@endcomponent

@section('content')
    <div class="container">
        <div class="t-contain">
            <div class="table-card" style="width: 75%">
                <table id="users" class="table" style="width: 100%">
                    <thead class="t-head">
                        <th class="t-point1">#</th>
                        <th></th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Birthdate</th>
                        <th>Ativo</th>
                        <th class="t-point2"></th>
                    </thead>
                </table>
            </div>
        </div>
        <span class="btn-container mt-3">
            <button class="btn btn-primary button-invite" data-bs-toggle="modal" data-bs-target="#inviteModal">Convidar
                Utilizadores</button>
            <button class="btn btn-outline-dark" onclick="window.location.href = 'users/pending'">Ver Pendentes</button>
        </span>
    </div>
@stop

<script>
    $(document).ready(() => {
        $("#confirm").on('click', () => {
            hasEmpty = animateErr(["invite_email", "types"]);
            if (hasEmpty) return;

            $.ajax({
                method: 'post',
                url: '/professional/admin/invite_users',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "email": $("#invite_email").val(),
                    "type": $("#types").val(),
                }
            }).done((res) => {
                successToast(res.title, res.message);                
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        })


        $("#users").dataTable({

            "ordering": false,

            "language": {
                "paginate": {
                    "next": '<i class="fa-solid fa-caret-right"></i>',
                    "previous": '<i class="fa-solid fa-caret-left"></i>'
                }
            },

            ajax: {
                method: "post",
                url: '/professional/admin/getallusers',
                data: {
                    "_token": $('#token').val(),
                },
                dataSrc: ''
            },
            columns: [{
                    data: "id",
                    width: "5%",
                    className: 'mid'
                },
                {
                    data: "pfp",
                    width: "10%",
                    render: function(data, type, row, meta) {
                        return "<img class='tab-pfp' src='/img/pfp/" + data + "'></span>";
                    }
                },
                {
                    data: "first_name",
                    width: "15%",
                    className: 'mid',
                    render: function(data, type, row, meta) {
                        return "<span>" + data + " " + row['last_name'] + "</span>";
                    }
                },
                {
                    data: "email",
                    width: "15%",
                    className: 'mid'
                },
                {
                    data: "birthdate",
                    width: "15%",
                    className: 'mid',
                    render: function(data, type, row, meta) {
                        var formattedDate = new Date(data);
                        var d = formattedDate.getDate();
                        var m = formattedDate.getMonth() + 1;
                        var y = formattedDate.getFullYear();
                        return "<span>" + d + "/" + m + "/" + y + "</span>";
                    }
                },
                {
                    data: "active",
                    width: "10%",
                    className: 'mid',
                    render: function(data, type, row, meta) {
                        return "<div class=\"form-check form-switch\">\
                            <input class=\"form-check-input\" type=\"checkbox\" role=\"switch\" id=\"check" + row[
                                'id'] + "\" " +
                            (data ? "checked" : "") + " " + (row['id'] ==
                                {{ session()->get('user.id') }} ? 'disabled' : '') + ">\
                        </div>"
                    }
                },
                {
                    data: null,
                    width: "5%",
                    className: 'mid',
                    render: function(data, type, row, meta) {
                        if (row['id'] == {{ session()->get('user.id') }}) return '';
                        return '<span>\
                                    <a href="#"><i class="fa-solid fa-eye" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i></a>\
                                </span>';
                    }
                },
            ]
        });
    })
</script>
