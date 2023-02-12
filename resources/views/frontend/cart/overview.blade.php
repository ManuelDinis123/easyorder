@include('layouts.includes')
@extends('layouts.clients.nav')

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
                <button class="btn btn-dark">Deixar Nota</button>
                <button class="btn btn-light">Acompanhamentos</button>
            </div>
        @endforeach
    @endforeach

    <script>
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
    </script>
@stop
