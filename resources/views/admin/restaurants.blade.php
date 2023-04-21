@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@extends('layouts.admin.sidebar', ['file' => 'restaurants'])

@section('content')

    <div class="container mt-3">
        <div class="reports-container">
            <table class="table" id="restaurants" style="width: 100%">
                <thead style="background-color: rgb(20, 20, 20); color:white">
                    <th class="t-point1">Nome</th>
                    <th>Descrição</th>
                    <th>Dono</th>
                    <th class="t-point2">Ativo</th>
                </thead>
            </table>
        </div>
    </div>

    <script>
        // Change restaurant to active or deactivated
        function changeState(id) {
            $.ajax({
                method: "post",
                url: "/admin/restaurantes/switch",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "active": $("#active" + id).is(":checked") ? 1 : 0
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#restaurants").DataTable().ajax.reload(null, false);
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }

        $(document).ready(() => {
            $("#restaurants").dataTable({
                "ordering": false,

                "language": {
                    "paginate": {
                        "next": '<i class="fa-solid fa-caret-right"></i>',
                        "previous": '<i class="fa-solid fa-caret-left"></i>'
                    }
                },

                ajax: {
                    method: "post",
                    url: "/admin/restaurantes/get",
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
                        data: "description",
                        width: "35%"
                    },
                    {
                        data: "owner",
                        width: "20%",
                    },
                    {
                        data: "active",
                        width: "20%",
                        render: function(data, type, row, meta) {
                            var isCheck = data == 1 ? "checked" : ";"
                            return `<div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" onclick="changeState(` + row
                                .id + `)" role="switch" id="active` + row
                                .id +
                                `" ` +
                                isCheck + `>
                                    </div>`
                        }
                    }
                ]
            });
        });
    </script>

@stop
