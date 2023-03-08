@include('layouts.includes')
@extends('layouts.clients.nav')

@component('components.modal_builder', [
    'modal_id' => 'notesModal',
    'hasHeader' => true,
    'rawHeader' =>
        '<h5 class="modal-title" id="notesModal"><i class="fa-solid fa-note-sticky text-icon"></i> Deixar Nota</h5>',
    'hasBody' => true,
    'rawBody' => '<textarea id="note" class="form-control" placeholder="Escreva aqui uma nota sobre este item!" rows="5"></textarea>
        <input type="hidden" id="itmID">',
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-light', 'dismiss' => true],
        ['label' => 'Guardar', 'id' => 'saveNote', 'class' => 'btn btn-dark'],
    ],
])
@endcomponent

@component('components.modal_builder', [
    'modal_id' => 'acompanhamentosModal',
    'hasHeader' => true,
    'rawHeader' =>
        '<h5 class="modal-title" id="acompanhamentosModal"><i class="fa-solid fa-french-fries text-icon"></i> Acompanhamentos</h5>',
    'hasBody' => true,
    'rawBody' => '<ul class="list-group list-group-flush" id="acomp_list"></ul>',
    'hasFooter' => true,
    'buttons' => [['label' => 'Fechar', 'id' => 'closeMdl2', 'class' => 'btn btn-dark', 'dismiss' => true]],
])
@endcomponent

@section('content')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">

    <div class="overview-header">
        <h1 class="oh-h1">Carinho de Compras <span id="count_header">{{ $count }} Items</span></h1>
        <input type="hidden" id="count" value="{{ $count }}">
        <hr>
    </div>

    @foreach ($cart as $items)
        @foreach ($items['items'] as $item)
            <div class="item-card-container" id="card{{ $item['item_id'] }}">
                <div class="item">
                    <img src="{{ $item['imageUrl'] }}" class="item-img">
                    <div class="item-info">
                        <h3>{{ $item['name'] }} <span id="quantity_for_{{ $item['item_id'] }}">x
                                {{ $item['quantity'] }}</span></h3>
                        <span class="total-price">Total: <span
                                id="ttlPrice{{ $item['item_id'] }}">{{ $item['price'] * $item['quantity'] }}€</span></span>
                        <div class="btnss">
                            <button class="btn btn-dark" class="minus-btn qntbtns"
                                onclick="cartAddRemove({{ $item['item_id'] }}, 1)"><i
                                    class="fa-solid fa-minus"></i></button>
                            <button class="btn btn-dark" class="plus-btn qntbtns"
                                onclick="cartAddRemove({{ $item['item_id'] }}, 0)"><i
                                    class="fa-solid fa-plus"></i></button>
                        </div>
                        <h4>{{ $items['name'] }}</h4>
                        <span class="description text-muted">{{ $item['description'] }}</span>
                    </div>
                </div>
                <input type="hidden" id="hidden{{ $item['item_id'] }}" value="{{ $item['quantity'] }}">
                <input type="hidden" id="base_price{{ $item['item_id'] }}" value="{{ $item['price'] }}">
            </div>
            <div class="card-btns" id="btns_for_{{ $item['item_id'] }}">
                <button class="btn btn-dark" onclick="noteMDL({{ $item['cart_item_id'] }})">Deixar Nota</button>
                <button class="btn btn-light"
                    onclick="open_modal({{ $item['item_id'] }}, {{ $item['cart_item_id'] }})">Acompanhamentos</button>
            </div>
        @endforeach
    @endforeach

    <div class="container">
        <label class="dpe_lbl">Data para entrega:</label>
        <input class="form-control dpe" type="datetime-local" id="deadline" name="deadline">
    </div>

    <button class="btn btn-dark form-control mt-5" {{ count($cart) <= 0 ? 'disabled' : '' }}
        style="margin-bottom:20px; height: 50px" id="confirmOrder">Confirmar
        Pedido</button>

    <script>
        var today = new Date().toISOString().slice(0, 16);
        document.getElementsByName("deadline")[0].min = today;

        // TODO: Later this should redirect to payment page and the confirm should be done there
        $("#confirmOrder").on('click', () => {

            var isInvalid = animateErr(["deadline"]);
            if (isInvalid) return;

            $.ajax({
                method: "post",
                url: "/createorder",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "deadline": $("#deadline").val()
                }
            }).done(res => {
                successToast(res.title, res.message);
                setTimeout(() => {
                    window.location.href = "/";
                }, 1000);
            }).fail(err => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        });

        function addRemoveAcompanhamentos(id, cart_item_id, remove) {
            // Turn text to the number value of quantity
            var quantity_before = $("#acp_" + id).text();
            quantity_before = quantity_before.replace("x ", "")
            quantity_before = parseInt(quantity_before);

            var new_quantity;
            if (remove) {
                new_quantity = quantity_before - 1;
                if (new_quantity == 0) {
                    $("#rmAC_" + id).attr("disabled", "disabled");
                };
            } else {
                $("#rmAC_" + id).removeAttr("disabled");
                new_quantity = quantity_before + 1;
            }

            var data_ajax = {
                "_token": "{{ csrf_token() }}",
                "quantity": new_quantity,
                "cart_item_id": cart_item_id,
                "side_id": id
            }

            if ($("#idfor_" + id).val() != 'none') {
                data_ajax["sdID"] = $("#idfor_" + id).val();
            }

            $.ajax({
                method: "post",
                url: "/addside",
                data: data_ajax
            }).done((res) => {
                console.log(res)
                $("#acp_" + id).text(" x " + new_quantity);
                $("#idfor_" + id).val(res.id);
            }).fail((err) => {
                console.log(err)
            })
        }

        function noteMDL(itemId) {
            $("#itmID").val(itemId);

            $.ajax({
                method: 'post',
                url: "/getnote",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": itemId
                }
            }).done((res) => {
                $("#note").val(res.note);
                $("#notesModal").modal("toggle");
            })

        }

        function cartAddRemove(itemID, isRemove = 0) {
            $.ajax({
                method: 'post',
                url: '/addToCart',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "item_id": itemID,
                    "isRemove": isRemove
                }
            }).done((res) => {
                if (isRemove && res == "deleted") {
                    $("#cart_total").text((isRemove ? parseInt($("#cart_total").text()) - 1 : parseInt($(
                            "#cart_total")
                        .text()) + 1));
                    $("#count").val(parseInt($("#count").val()) - 1);
                    $("#count_header").text($("#count").val() + " Items");
                    $("#card" + itemID).remove();
                    $("#btns_for_" + itemID).remove();
                    return;
                }
                var quantity = parseInt($("#hidden" + itemID).val());
                var quantity = (isRemove ? quantity - 1 : quantity + 1);
                $("#hidden" + itemID).val(quantity);
                $("#quantity_for_" + itemID).text("x " + quantity);
                $("#ttlPrice" + itemID).text((parseInt($("#base_price" + itemID).val()) * quantity) + "€");

                $("#cart_total").text((isRemove ? parseInt($("#cart_total").text()) - 1 : parseInt($("#cart_total")
                    .text()) + 1));

                $("#count").val(isRemove ? parseInt($("#count").val()) - 1 : parseInt($("#count").val()) + 1);
                $("#count_header").text($("#count").val() + " Items");
            })
        }

        // Opens the ingredients modal
        function open_modal(id, cart_item_id) {
            $("#acomp_list li").remove();
            $("#acomp_list span").remove();
            $.ajax({
                method: "post",
                url: '/getsides',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id,
                    "cart_item_id": cart_item_id,
                },
            }).done((res) => {
                console.log(res)
                if (res.length != 0) {
                    $.each(res, (key, val) => {
                        $("#acomp_list").append(
                            '<li class="list-group-item d-flex justify-content-between align-items-center">\
                            <div><span class="text-muted">'+val['quantity_type']+'</span><br />\
                                <span>' + val["ingredient"] + '</span><span class="fw-bold" id="acp_' +
                            val[
                                "id"] +
                            '"> x ' + (val["quantity"] == null ? 0 : val["quantity"]) +
                            '</span></div>' +
                            '\
                            <div><button onclick="addRemoveAcompanhamentos(' + val["id"] + ', ' +
                            cart_item_id +
                            ', 0)" class="btn btn-dark" style="margin-right:6px"><i class="fa-solid fa-plus"></i></button><button class="btn btn-dark" onclick="addRemoveAcompanhamentos(' +
                            val["id"] + ', ' + cart_item_id + ', 1)" id="rmAC_' + val["id"] + '" '+(val["quantity"] == null ? "disabled" : "")+'><i class="fa-solid fa-minus"></i></button></div>\
                            <input type="hidden" id="idfor_' + val["id"] + '" value="none"></li>'
                        );
                    })
                } else {
                    $("#acomp_list").append(
                        '<span class="text-muted" style="display: flex; justify-content: center;">Este item não tem acompanhamentos</span>'
                    );
                }
            });
            $("#acompanhamentosModal").modal("toggle");
        }

        $(document).ready(() => {
            $("#closeMdl").on('click', () => {
                $("#note").val("");
            });

            $("#saveNote").on('click', () => {
                var note = $("#note").val();
                var cart_item_id = $("#itmID").val();
                $.ajax({
                    method: 'post',
                    url: '/addnote',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "note": note,
                        "cart_item_id": cart_item_id,
                    }
                }).done((res) => {
                    successToast(res.title, res.message)
                    $("#note").val("");
                    $("#notesModal").modal("toggle");
                }).fail((err) => {
                    errorToast(err.responseJSON.tilte, err.responseJSON.message);
                })
            })
        });
    </script>
@stop
