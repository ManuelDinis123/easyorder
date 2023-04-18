@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@extends('layouts.admin.sidebar', ['file' => 'users'])

@component('components.modal_builder', [
    'modal_id' => 'confirmation',
    'hasBody' => true,
    'rawBody' => '<h3><i class="fa-solid fa-triangle-exclamation" style="color: #aa1313;"></i> Tem a certeza que quer realizar esta ação?</h3>
                  <span class="text-muted">Isto ira a afetar a conta do user</span>
                  <input type="hidden" id="activation">
                  <input type="hidden" id="user_id">',
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
        ['label' => 'Confirmar', 'id' => 'confirm', 'class' => 'btn btn-primary'],
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
                    <th class="t-point2">App Admin</th>
                </thead>
            </table>
        </div>
    </div>

    <script>
        function switches(id, isAppAdmin) {
            $("#action").val(isAppAdmin ? "appadmin" : "active");
            $("#user_id").val(id);
            $("#confirmation").modal("toggle");
        }

        $(document).ready(() => {
            $("#confirm").on('click', () => {
                $.ajax({
                    method: "post",
                    url: '/admin/users/appadmin',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "user_id": $("#user_id").val(),
                        "active": $("#app_admin" + $("#user_id").val()).is(":checked") ? 1 : 0,
                    }
                }).done((res) => {
                    successToast(res.title, res.message);
                    $("#users").DataTable().ajax.reload(null, false);
                }).fail((err) => {
                    errorToast(err.responseJSON.title, err.responseJSON.message);
                });
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
                            var isCheck = data == 1 ? "checked" : ";"
                            return `<div class="form-check form-switch">
                                        <input class="form-check-input" onclick="switches(` + row.id +
                                `, 1)" type="checkbox" role="switch" id="app_admin` + row.id + `" ` +
                                isCheck + `>
                                    </div>`
                        }
                    },
                ]
            });
        })
    </script>

@stop
