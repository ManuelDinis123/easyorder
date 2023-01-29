@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'orders'])

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

@section('content')

    <div class="row">
        <div class="col-12">
            <button class="btn is-selected" id="ongoingbtn">Em progresso</button>
            <button class="btn not-selected" id="closedbtn">Finalizados</button>
            <button class="btn not-selected" id="cancelledbtn">Cancelados</button>
        </div>
    </div>

    <hr>

    <div>
        <div id="ongoing" class="orders-container">
            <table class="table table-bordered mt-3 orders-table" id="orders">
                <thead class="orders-table-header">
                    <th class="t-point1">Cliente</th>
                    <th>Data de Entrega</th>
                    <th>Progresso</th>
                    <th class="t-point2"></th>
                </thead>
            </table>
        </div>
        <div class="visually-hidden orders-container" id="closed">
            <table class="table table-bordered mt-3 orders-table" id="orders_closed">
                <thead class="orders-table-header">
                    <th class="t-point1">Cliente</th>
                    <th>Data de Entrega</th>
                    <th>Progresso</th>
                    <th class="t-point2"></th>
                </thead>
            </table>
        </div>
        <div class="visually-hidden orders-container" id="cancelled">
            <table class="table table-bordered mt-3 orders-table" id="orders_cancelled">
                <thead class="orders-table-header">
                    <th class="t-point1">Cliente</th>
                    <th>Data de Entrega</th>
                    <th>Progresso</th>
                    <th class="t-point2"></th>
                </thead>
            </table>
        </div>
    </div>
@stop

<script>
    // Changes tabs
    function changeTabs(changeTo, remove) {        
        $("#" + changeTo).removeClass("visually-hidden");
        $("#" + changeTo + 'btn').addClass("is-selected");
        $("#" + changeTo + 'btn').removeClass("not-selected");
        $("#" + changeTo).addClass("animate__animated animate__fadeIn");

        $.each(remove, (key, val) => {
            if (!$("#" + val).hasClass("visually-hidden")) {
                $("#" + val).addClass("visually-hidden");
            }
            if (!$("#" + val + 'btn').hasClass("not-selected")) {
                $("#" + val + 'btn').addClass("not-selected");
                $("#" + val + 'btn').removeClass("is-selected");
            }
            $("#" + val).removeClass("animate__animated animate__fadeIn");
        });
    }

    $("#document").ready(() => {

        $("#closedbtn").on('click', () => {
            changeTabs("closed", ["ongoing", "cancelled"]);
        });
        $("#ongoingbtn").on('click', () => {
            changeTabs("ongoing", ["closed", "cancelled"]);
        });
        $("#cancelledbtn").on('click', () => {
            changeTabs("cancelled", ["ongoing", "closed"]);
        });


        // datatables
        map = [{
                'id': 'orders',
                'closed': 0,
                'cancelled': 0,
            },
            {
                'id': 'orders_closed',
                'closed': 1,
                'cancelled': 0,
            },
            {
                'id': 'orders_cancelled',
                'closed': 0,
                'cancelled': 1,
            }
        ];

        $.each(map, (key, val) => {
            $("#" + val.id).dataTable({

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
                        "_token": "{{ csrf_token() }}",
                        "closed": val.closed,
                        "cancelled": val.cancelled
                    },
                    dataSrc: ''
                },
                columns: [
                    {
                        data: "full_name",
                        width: "25%"
                    },
                    {
                        data: "deadline",
                        width: "25%",
                        render: function(data, type, row, meta) {
                            return (data >= '{{ date('Y-m-d h:i:s') }}') ? '<span>' +
                                data +
                                '</span>' : ((row['closed']) ? '<span>' + data +
                                    '</span>' :
                                    '<span style="color: #dc3545; font-weight: 600">' +
                                    data + '</span>')
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
        })
    });
</script>
