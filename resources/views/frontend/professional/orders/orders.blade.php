@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'orders'])

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

@section('content')
    <div class="orders-container">
        <table class="table table-bordered mt-3 orders-table" id="orders">
            <thead class="orders-table-header">
                <th>Cliente</th>
                <th>Data de Entrega</th>
                <th>Progresso</th>
                <th></th>
            </thead>
        </table>
    </div>
@stop

<script>
    $("#document").ready(() => {


        $("#orders").dataTable({

            "ordering": false,
            "autoWidth": false,

            "language": {
                "paginate": {
                    "next": '<i class="fa-solid fa-caret-right"></i>',
                    "previous": '<i class="fa-solid fa-caret-left"></i>'
                }
            },
            ajax: {
                method: "post",
                url: 'getorders',
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataSrc: ''
            },
            columns: [{
                    data: "full_name",
                    width: "25%"
                },
                {
                    data: "deadline",
                    width: "25%",
                    render: function(data, type, row, meta) {
                        return (data >= '{{date("Y-m-d h:i:s")}}') ? '<span>'+data+'</span>' : '<span style="color: #dc3545; font-weight: 600">'+data+'</span>'
                    }
                },
                {
                    data: "progress",
                    width: "47%",
                    render: function(data, type, row, meta) {
                        return '<span>\
                          <div class="progress">\
                              <div class="progress-bar" role="progressbar" aria-valuenow="' + data + '" aria-valuemin="0" aria-valuemax="100"\
                              style="width:' + data + '%">\
                                <span class="sr-only">' + data + '% Complete</span>\
                              </div>\
                            </div>\
                            </span>';
                    }
                },
                {
                    data: null,
                    width: "3%",
                    render: function(data, type, row, meta) {
                        return '<span>\
                                    <a href="/professional/encomendas/' + row.id + '"><i class="fa-solid fa-eye" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i></a>\
                                </span>';
                    }
                },
            ]
        });
    });
</script>
