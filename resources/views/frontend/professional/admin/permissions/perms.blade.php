@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'perms'])

<link rel="stylesheet" href="{{ asset('css/perms.css') }}">

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

                        return '<div  style="display: flex; align-items: center">\
                        <i class="fa-sharp fa-solid fa-pen" style="color:#1C46B2; cursor:pointer; margin-right:5px;"></i>\
                        <i class="fa-sharp fa-solid fa-trash-xmark" style="color:#bf1313; cursor:pointer;"></i>\
                        </div>';
                    }
                },
            ]
        });
    });
</script>
