@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@extends('layouts.admin.sidebar', ['file' => 'users'])

@component('components.modal_builder', [
    'modal_id' => 'confirmation',
    'hasBody' => true,
    'rawBody' => '<h3><i class="fa-solid fa-triangle-exclamation" style="color: #aa1313;"></i> Tem a certeza que quer tornar este user em Admin?</h3>
                  <span class="text-muted">Isto vai dar acesso á admin dashboard a este user</span>',
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
        ['label' => 'Confirmar', 'id' => 'confirm', 'class' => 'btn btn-primary'],
    ],
])
@endcomponent

@component('components.modal_builder', [
    'modal_id' => 'banModal',
    'hasBody' => true,
    'rawBody' => '<h3><i class="fa-solid fa-triangle-exclamation" style="color: #aa1313;"></i> Tem a certeza que quer banir este user?</h3>
                  <span class="text-muted">Ele deixara de ter acesso á plataforma</span>',
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeBan', 'class' => 'btn btn-danger', 'dismiss' => true],
        ['label' => 'Confirmar', 'id' => 'confirmBan', 'class' => 'btn btn-primary'],
    ],
])
@endcomponent

@section('content')
    <div class="container mt-3">
        <div class="reports-container">
            <table class="table" id="users" style="width: 100%">
                <thead style="background-color: rgb(20, 20, 20); color:white">
                    <th class="t-point1">Nome</th>
                    <th>Email</th>
                    <th>Data de Nascimento</th>
                    <th>Pro</th>
                    <th>App Admin</th>
                    <th class="t-point2">Banido</th>
                </thead>
            </table>
        </div>
    </div>

    <input type="hidden" id="activation">
    <input type="hidden" id="user_id">

    <script>
        let lastSwitch = "none";

        function switches(id, isAppAdmin, isBan = false) {
            $("#user_id").val(id);
            $(isBan == true ? "#banModal" : "#confirmation").modal("toggle");
            lastSwitch = ((isBan) ? "banned" : "app_admin") + id;
        }

        $(document).ready(() => {
            $("#confirm").on('click', () => {
                activeApi("appadmin");
            })
            $("#confirmBan").on('click', () => {
                activeApi("ban");
            })

            // Used for ajax of banning users or app admin
            function activeApi(action) {
                var activeID = ((action == "ban") ? "#banned" : "#app_admin") + $("#user_id").val();
                console.log($(activeID).is(":checked"));
                $.ajax({
                    method: "post",
                    url: ((action == "ban") ? '/admin/users/ban' : '/admin/users/appadmin'),
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "user_id": $("#user_id").val(),
                        "active": $(activeID).is(":checked") ? 1 : 0,
                    }
                }).done((res) => {
                    successToast(res.title, res.message);
                    $("#users").DataTable().ajax.reload(null, false);
                    $((action == "ban") ? "#banModal" : "#confirmation").modal("toggle");
                    lastSwitch = "none";
                }).fail((err) => {
                    errorToast(err.responseJSON.title, err.responseJSON.message);
                });
            }

            $("#confirmation").on('hidden.bs.modal', () => {
                revertSwitchState(lastSwitch);
            })
            $("#banModal").on('hidden.bs.modal', () => {
                revertSwitchState(lastSwitch);
            })

            function revertSwitchState(id) {
                if ($("#" + id).is(":checked")) {
                    $("#" + id).prop("checked", false)
                    return;
                }
                $("#" + id).prop("checked", true)
            }

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
                    url: "/admin/users/get",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: "name",
                        width: "25%"
                    },
                    {
                        data: "email",
                        width: "25%"
                    },
                    {
                        data: "birthdate",
                        width: "20%",
                        render: function(data, type, row, meta) {
                            var format = new Date(data);
                            format = format.getDate() + '/' + (format.getMonth() + 1) + '/' + format
                                .getFullYear();
                            return format;
                        }
                    },
                    {
                        data: "isProfessional",
                        width: "10%",
                        render: function(data, type, row, meta) {
                            var isCheck = data == 1 ? "checked" : ";"
                            return `<div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="isPro" ` +
                                isCheck + ` disabled>
                                    </div>`
                        }
                    },
                    {
                        data: "app_admin",
                        width: "10%",
                        render: function(data, type, row, meta) {
                            var isCheck = data == 1 ? "checked disabled" : ";"
                            return `<div class="form-check form-switch">
                                        <input class="form-check-input" onclick="switches(` + row.id +
                                `, 1)" type="checkbox" role="switch" id="app_admin` + row.id +
                                `" ` +
                                isCheck + `>
                                    </div>`
                        }
                    },
                    {
                        data: "banned",
                        width: "10%",
                        render: function(data, type, row, meta) {
                            var isCheck = data == 1 ? "checked" : ";"
                            var disable = row.app_admin ? "disabled" : "";
                            return `<div class="form-check form-switch">
                                        <input class="form-check-input" onclick="switches(` + row.id +
                                `, 1, true)" type="checkbox" role="switch" id="banned` + row.id +
                                `" ` +
                                isCheck + ` ` + disable + `>
                                    </div>`
                        }
                    },
                ]
            });
        })
    </script>

@stop
