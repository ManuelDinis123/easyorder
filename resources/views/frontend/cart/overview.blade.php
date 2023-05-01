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
    <div class="loaderFADE visually-hidden">
        <div class="loader-container" id="lc">
            <div class="loader2"></div>
        </div>
    </div>
    <div class="overview-header">
        <h1 class="oh-h1">Carinho de Compras <span id="count_header">{{ $count }} Items</span></h1>
        <input type="hidden" id="count" value="{{ $count }}">
        <hr>
    </div>

    @foreach ($cart as $items)
        @foreach ($items['items'] as $item)
            <div class="item-card-container" id="card{{ $item['item_id'] }}">
                <div class="item">
                    <img src="{{ isset($item['imageUrl'])?$item['imageUrl']:'https://trello.com/1/cards/642f03e28350900aa3aac4ee/attachments/6430690d990221cd112dbc0f/download/image.png' }}" class="item-img">
                    <div class="item-info">
                        <h3>{{ $item['name'] }} <span id="quantity_for_{{ $item['item_id'] }}">x
                                {{ $item['quantity'] }}</span></h3>
                        <span class="total-price">Total: <span
                                id="ttlPrice{{ $item['item_id'] }}">{{ $item['price'] * $item['quantity'] + $item['addition'] }}€</span></span>
                        <div class="btnss">
                            <button class="btn btn-dark" class="minus-btn qntbtns"
                                onclick="cartAddRemove({{ $item['item_id'] }}, 1, {{ $item['cart_item_id'] }})"><i
                                    class="fa-solid fa-minus"></i></button>
                            <button class="btn btn-dark" class="plus-btn qntbtns"
                                onclick="cartAddRemove({{ $item['item_id'] }}, 0, {{ $item['cart_item_id'] }})"><i
                                    class="fa-solid fa-plus"></i></button>
                        </div>
                        <h4>{{ $items['name'] }}</h4>
                        <span class="description text-muted">{{ $item['description'] }}</span>
                    </div>
                </div>
                <input type="hidden" id="hidden{{ $item['item_id'] }}" value="{{ $item['quantity'] }}">
                <input type="hidden" id="base_price{{ $item['item_id'] }}" value="{{ $item['default_price'] }}">
                <input type="hidden" id="sidePrices{{ $item['item_id'] }}" value="{{ $item['side_prices']['price'] }}">
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
        <input class="form-control dpe" type="datetime-local" min="{{ date('Y-m-d\TH:i') }}" max="{{ date('Y-m-d\TH:i', strtotime('+30 days')) }}" id="deadline" name="deadline">
    </div>


    <button class="btn btn-dark form-control mt-5" {{ count($cart) <= 0 ? 'disabled' : '' }}
        style="margin-bottom:20px; height: 50px" id="confirmOrder">Confirmar
        Pedido</button>


    <form action="/checkout" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="deadline" id="hiddenDeadline" value="">
        <button id="check_out_form" class="visually-hidden"></button>
    </form>

    <script>
        var today = new Date().toISOString().slice(0, 16);
        document.getElementsByName("deadline")[0].min = today;

        function submitForm() {
            var deadline = document.getElementById('deadline').value;
            if (deadline) {
                document.getElementById('checkout-form').submit();
            } else {
                alert('Por favor, selecione uma data para entrega.');
                return false;
            }
        }        
        $("#confirmOrder").on('click', () => {
            var isInvalid = animateErr(["deadline"]);
            if (isInvalid) return;
            $(".loaderFADE").removeClass("visually-hidden");
            $("#hiddenDeadline").val($("#deadline").val());

            $("#check_out_form").click();
        });

        function addRemoveAcompanhamentos(id, cart_item_id, remove, price = 0, itmID) {
            // Turn text to the number value of quantity
            var quantity_before = $("#acp_" + id).text();
            quantity_before = quantity_before.replace("x ", "")
            quantity_before = parseInt(quantity_before);

            // Add to price
            var price_b = $("#ttlPrice" + itmID).text();
            price_b = price_b.replace('€', '');

            console.log("price ", price);

            var new_quantity;
            if (remove) {
                $("#ttlPrice" + itmID).text(((parseInt(price_b) - price) + '€'));
                $("#sidePrices" + itmID).val(parseInt($("#sidePrices" + itmID).val()) - price);
                new_quantity = quantity_before - 1;
                if (new_quantity == 0) {
                    $("#rmAC_" + id).attr("disabled", "disabled");
                };
            } else {
                $("#ttlPrice" + itmID).text(((parseInt(price_b) + price) + '€'));
                $("#sidePrices" + itmID).val(parseInt($("#sidePrices" + itmID).val()) + price);
                $("#rmAC_" + id).removeAttr("disabled");
                new_quantity = quantity_before + 1;
            }
            console.log("sidePrices Hidden Input ", $("#sidePrices" + itmID).val());
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
                $("#acp_" + id).text(" x " + new_quantity);
                $("#idfor_" + id).val(res.id);
            }).fail((err) => {})
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

        function cartAddRemove(itemID, isRemove = 0, cid) {
            $.ajax({
                method: 'post',
                url: '/addToCart',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "item_id": itemID,
                    "isRemove": isRemove,
                    "cart_item_id": cid
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
                $("#sidePrices" + itemID).val(res.to_add.price);
                $("#ttlPrice" + itemID).text(((parseInt($("#base_price" + itemID).val()) * quantity) + parseInt(($(
                    "#sidePrices" + itemID).val() ? $("#sidePrices" + itemID).val() : 0))) + "€");
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
                if (res.length != 0) {
                    $.each(res, (key, val) => {
                        $("#acomp_list").append(
                            '<li class="list-group-item d-flex justify-content-between align-items-center">\
                                                    <div><span class="text-muted">' + val['quantity_type'] + '</span><br />\
                                                        <span>' + val["ingredient"] +
                            '</span><span class="fw-bold" id="acp_' +
                            val["id"] + '"> x ' + (val["quantity"] == null ? 0 : val["quantity"]) +
                            '</span></div>' + '\
                                                    <div><label class="price_sides">' + val["price"] +
                            '€</label><button onclick="addRemoveAcompanhamentos(' + val["id"] + ', ' +
                            cart_item_id +
                            ', 0, ' + val['price'] + ', ' + id +
                            ')" class="btn btn-dark" style="margin-right:6px"><i class="fa-solid fa-plus"></i></button><button class="btn btn-dark" onclick="addRemoveAcompanhamentos(' +
                            val["id"] + ', ' + cart_item_id + ', 1, ' + val['price'] + ', ' + id +
                            ')" id="rmAC_' + val["id"] + '" ' + (val["quantity"] == null ? "disabled" :
                                "") + '><i class="fa-solid fa-minus"></i></button></div>\
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
