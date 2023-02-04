@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'edit_users'])

<link rel="stylesheet" href="{{ asset('css/admin_users.css') }}">

@component('components.delete', [
    'modal_id' => 'confirmModal',
    'function_name' => 'remove',
    'hidden' => 'tk',
])
    @slot('title')
        Tem a certeza que quer cancelar este convite?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent

@section('content')

    <div class="container">
        <div class="t-contain">
            <div class="table-card" style="width: 50%">
                <table class="table" style="width: 100%" id="pending">
                    <thead class="t-head">
                        <th class="t-point1"></th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th class="t-point2"></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@stop

<script>
    
    // Opens the confirmation modal for removing invites
    function confirmationModal(id) {
        $("#confirmModal").modal('toggle');        
        $("#tk").val(id);
    }

    // delete item from DB
    function remove() {
        $("#confirmModal").modal('toggle');
        removeDB("/professional/admin/delete_invite", $("#tk").val());
        $("#pending").DataTable().ajax.reload(null, false);
    }

    $(document).ready(() => {
        $("#pending").dataTable({

            "ordering": false,

            "language": {
                "paginate": {
                    "next": '<i class="fa-solid fa-caret-right"></i>',
                    "previous": '<i class="fa-solid fa-caret-left"></i>'
                }
            },

            ajax: {
                method: "post",
                url: '/professional/admin/get_pending',
                data: {
                    "_token": $('#token').val(),
                },
                dataSrc: ''
            },
            columns: [{
                    data: null,
                    width: "5%",
                    render: function(data, type, row, meta) {
                        return '<i class="fa-regular fa-clock-two tb-clock"></i>';
                    }
                },
                {
                    data: "email",
                    width: "30%",
                    className: "mid"
                },
                {
                    data: "label",
                    width: "15%",
                    className: "mid"
                },
                {
                    data: null,
                    width: "5%",
                    className: 'mid',
                    render: function(data, type, row, meta) {
                        return "<span>\
                            <i onClick=\"confirmationModal('" + row.token + "')\" class=\"fa-sharp fa-solid fa-trash-clock\" style=\"color:#bf1313; cursor:pointer;\"></i>\
                            </span>";
                    }
                },
            ]
        });
    });
</script>
