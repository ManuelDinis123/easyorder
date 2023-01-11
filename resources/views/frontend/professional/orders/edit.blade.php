@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'orders'])

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<style>
    .card-contain {
        width: 505px;
        height: 202px;
        border-radius: 14px;
        cursor: pointer;
        overflow: hidden
    }

    .item-card {
        border-radius: 14px;
        background-size: 100%;
        background-position: center;
        -webkit-transition: all .4s;
        transition: all .4s ease-in-out;
    }

    .item-card:hover {
        background-size: 105%;
    }

    .item-gradient {
        opacity: 1;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%);
        -webkit-transition: all .4s;
        transition: all .4s ease-in-out;
    }

    .item-gradient:hover {
        opacity: .7;
    }

    .dataTables_info {
        display: none;
    }
</style>

<div class="modal fade" id="ingredientsModal" aria-labelledby="ingredientsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Detalhes:</h4>
            </div>
            <div class="modal-body">
                <span class="headers-details"><i class="fa-solid fa-utensils"></i> Acompanhamentos:</span>
                <ul class="list-group list-group-flush" id="ing_list"></ul>
                <hr>
                <span class="headers-details"><i class="fa-solid fa-exclamation"></i> Nota:</span>
                <div class="note-card mt-3">
                    <div class="note-text-container">
                        <label class="note-text"></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal">Sair</button>
            </div>
        </div>
    </div>
</div>

@section('content')
    <div class="row">
        <div class="col-6">
            <span>Pedido de</span>
            <h3 style="font-weight: 700">{{ $first_name . ' ' . $last_name }}</h3>
        </div>
        <div class="col-6">
            <div style="float: right; margin-right:70px">
                <span>Data de entrega</span>
                <div class="deadline-contain">
                    @if($deadline >= date("Y-m-d h:i:s"))
                    <h3 style="font-weight: 700;">{{ $deadline }}</h3>
                    @else
                    <h3 class="deadline-warning"><i class="fa-solid fa-exclamation" style="font-size: 30px"></i> {{ $deadline }}</h3>
                    @endif
                    <button class="btn btn-dark close-order">Fechar Pedido</button>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row edit-contain">
        <div class="col-4">
            <div class="items-list">
                <div class="pt-2 item-card-content">
                    <div class="scroll-card">
                        {{-- Creation of food cards --}}
                        @foreach ($items as $item)
                            <div class="card-contain mt-3">
                                <div class="item-card" style="background-image: url({{ $item['imageUrl'] }});"
                                    onclick="open_modal({{ $item['id'] }}, '{{ $item['note'] }}')">
                                    <div class="item-gradient">
                                        <label class="food-card-label">{{ $item['name'] }}</label>
                                        <label class="food-card-quantity"> x {{ $item['quantity'] }}</label>
                                        <label class="food-card-price">{{ $item['price'] * $item['quantity'] }}€</label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="item-card-footer mt-3">
                        <label class="total-lbl">Total:</label>
                        <label class="total-lbl-price">{{ $total_price }}€</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-7">
            {{-- Progress --}}
            <h2>Progresso:</h2>
            <label id="progressLBL" class="percentage-lbl">{{ $progress }}% Completo</label>
            <span>
                <div class="progress">
                    <div id="progress_bar" class="progress-bar" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0"
                        aria-valuemax="100" style="width:{{ $progress }}%">
                        <span class="sr-only" id="sronly">{{ $progress }}% Complete</span>
                    </div>
                </div>
            </span>

            <hr class="mt-4">

            {{-- Items table to mark as done --}}
            <div class="ingredients-container pt-2">
                <table class="table table-striped table-borderless ing-tab" id="markDoneTab">
                    <thead>
                        <th></th>
                        <th></th>
                    </thead>
                </table>
            </div>

        </div>
    </div>
@stop

<script>
    // Opens the ingredients modal and adds the 
    function open_modal(id, note) {
        if (note) {
            $(".note-text").removeClass('text-muted');
            $(".note-text").text(note);
        } else {
            $(".note-text").text("Sem nota adicional");
            if (!$(".note-text").hasClass('text-muted')) {
                $(".note-text").addClass('text-muted');
            }
        }
        $("#ing_list li").remove();
        $("#ing_list span").remove();
        $.ajax({
            method: "post",
            url: '/professional/getingredients',
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id
            },
        }).done((res) => {
            if (res.length != 0) {
                $.each(res, (key, val) => {
                    $("#ing_list").append(
                        '<li class="list-group-item d-flex justify-content-between align-items-center">\
                            ' + val["ingredient"] +
                        '\
                            <span class="badge bg-primary rounded-pill" style="background-color: #1C46B2 !important">' +
                        val[
                            "quantity"] + '</span>\
                        </li>'
                    );
                })
            } else {
                $("#ing_list").append(
                    '<span class="text-muted" style="display: flex; justify-content: center;">Este item não tem acompanhamentos registados</span>'
                );
            }
        });
        $("#ingredientsModal").modal("toggle");
    }

    // Marks items as done or undone
    function mark(order_item_id, isDone) {
        $.ajax({
            method: "post",
            url: "/professional/changeordersitemstatus",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": order_item_id,
                "isDone": isDone,
                "order_id": {{ $id }}
            }
        }).done((res) => {
            (res.status == "Sucesso" ? successToast(res.status, res.message) : errorToast(res.status, res
                .message));
            $("#markDoneTab").DataTable().ajax.reload(null, false);

            // Update progress with new values            
            $('#progress_bar').attr('aria-valuenow', res.progress).css('width', res.progress+'%');
            $("#progressLBL").text(res.progress + '% Completo');
            $("#sronly").text(res.progress + '% Complete');

        })
    }

    $(document).ready(() => {
        $("#markDoneTab").dataTable({
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
                url: '/professional/getorderitems',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": {{ $id }},
                },
                dataSrc: ''
            },
            columns: [{
                    data: "name",
                    width: "70%",
                    render: function(data, type, row, meta) {
                        return '<span>' + row["name"] + '<i class="fa-sharp fa-solid ' + (row[
                                'done'] ? ' fa-check check' : 'fa-xmark xmark') +
                            '"></i></span>'
                    }
                },
                {
                    data: null,
                    width: "30%",
                    render: function(data, type, row, meta) {
                        return (!row['done']) ?
                            '<button class="btn btn-primary table-btn" onClick="mark(' + row[
                                "order_item_id"] + ', 1)" >Marcar como pronto</button>' :
                            '<button class="btn btn-danger table-btn" onClick="mark(' + row[
                                "order_item_id"] + ', 0)">Desmarcar</button>'
                    }
                }
            ]
        });
    });
</script>
