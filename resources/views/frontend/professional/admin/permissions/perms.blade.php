@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'perms'])

<link rel="stylesheet" href="{{ asset('css/perms.css') }}">

{{-- Confirm delete modal --}}
@component('components.delete', [
    'modal_id' => 'confirmModal',
    'function_name' => 'remove',
    'hidden' => 'item_id',
])
    @slot('title')
        Tem a certeza que quer remover este item?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent

@section('content')
    <div class="perms-container">
        <div class="perms-card">
            <table class="table table-striped" id="types" style="width: 100%">
                <thead>
                    <th>#</th>
                    <th>Nome</th>
                    <th><a href="permissions/criar"><i class="fa-solid fa-plus"></i></a></th>
                </thead>
            </table>
        </div>

    @stop

    <script>
        // Opens the confirmation modal
        function confirmationModal(id) {
            $("#confirmModal").modal('toggle');

            $("#item_id").val(id);
        }

        // Goes to the edit page
        function goEdit(id) {
            window.location.href = "permissions/" + id;
        }

        // delete item from DB
        function remove() {
            $("#confirmModal").modal('toggle');
            removeDB("/professional/admin/permissions/remove_types", $("#item_id").val());
            $("#types").DataTable().ajax.reload(null, false);
        }
        $(document).ready(() => {
            $("#types").dataTable({

                "ordering": false,

                "language": {
                    "paginate": {
                        "next": '<i class="fa-solid fa-caret-right"></i>',
                        "previous": '<i class="fa-solid fa-caret-left"></i>'
                    }
                },

                ajax: {
                    method: "post",
                    url: '/professional/admin/permissions/get_types',
                    data: {
                        "_token": $('#token').val(),
                    },
                    dataSrc: ''
                },
                columns: [{
                        data: "id",
                        width: "10%"
                    },
                    {
                        data: "label",
                        width: "10%",
                    },
                    {
                        data: null,
                        width: "10%",
                        render: function(data, type, row, meta) {
                            if (row.label == "Owner") return '';
                            if(row.id=={{session()->get("type.id")}}) return '';
                            return '<div  style="display: flex; align-items: center">\
                                    <i class="fa-sharp fa-solid fa-pen" onClick="goEdit(' + row.id + ')" style="color:#1C46B2; cursor:pointer; margin-right:5px;"></i>\
                                    <i onClick="confirmationModal(' + row.id + ')" class="fa-sharp fa-solid fa-trash-xmark" style="color:#bf1313; cursor:pointer;"></i>\
                                    </div>';
                        }
                    },
                ]
            });
        });
    </script>
