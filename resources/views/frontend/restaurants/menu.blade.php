@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    @include('components.frontend.restaurantstop', ['selected' => 'menu'])

    <style>
        .item-card {
            width: 215px;
            height: 215px;
            margin: 5px;
        }
    </style>

    <div class="container">
        <div class="menu-card">
            <div class="menu-card-header">
                <div class="row">
                    <div class="col-6">
                        <h1>Ementa</h1>
                        <hr>
                    </div>
                    <div class="col-6">
                        <span class="menu-card-ico" id="li-view"><i class="fa-regular fa-list"></i></span>
                        <span class="menu-card-ico choosen" id="crd-view"><i class="fa-regular fa-table"></i></span>
                    </div>
                </div>
            </div>
            <div class="menu-card-body">
                <div class="card-view" id="card_view">
                    <div class="row g-0">
                        @foreach ($items as $item)
                            <style>
                                .item{{ $item['id'] }} {
                                    height: 216px;
                                    transition: all 0.5s;
                                    -webkit-transition: all 0.5s;
                                    position: relative;
                                    background: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%), url("{{ isset($item['imageUrl']) ? $item['imageUrl'] : 'https://trello.com/1/cards/642f03e28350900aa3aac4ee/attachments/6430690d990221cd112dbc0f/download/image.png' }}");
                                    background-size: cover;
                                    background-position: center;
                                    border-radius: 31px;
                                    z-index: 2;
                                }
                            </style>
                            <div class="item-card">
                                <div id="{{ $item['id'] }}" class="item{{ $item['id'] }} menu_card"
                                    style="padding:0px 10px; margin-bottom:24px;">
                                    <div class="item-cnts g-0 unselectable">
                                        <span class="item-name unselectable">{{ $item['name'] }}</span>
                                        <span class="item-price unselectable"
                                            style="float: left">{{ $item['price'] + 0 }}€</span>
                                    </div>
                                </div>
                                <div class="mt-2 buttons-contain" id="buttons{{ $item['id'] }}">
                                    <span>
                                        <button class="btn btn-num cart-btns rem-btns"
                                            id="remove{{ $item['id'] }}">-</button>
                                    </span>
                                    <span>
                                        <button class="btn btn-mid cart-btns" id="qnt{{ $item['id'] }}">1 no cart</button>
                                        <input type="hidden" id="item_quantity{{ $item['id'] }}" value="0">
                                    </span>
                                    <span>
                                        <button class="btn btn-num cart-btns add-btn" style="margin-left: 20px;"
                                            id="additem{{ $item['id'] }}">+</button>
                                    </span>
                                </div>
                            </div>
                            <br>
                        @endforeach
                    </div>
                </div>
                <div class="list-view hide-view visually-hidden" id="list_view">
                    <div class="list-item">
                        @foreach ($items as $item)
                            <div class="row">
                                <div class="col-sm-11">
                                    <li class="menu-item unselectable" id="li-{{ $item['id'] }}">
                                        <div class="menu-item-info">
                                            <div class="menu-item-name">{{ $item['name'] }}<span
                                                    class="quantity-list visually-hidden" id="qntli{{ $item['id'] }}"> x
                                                    1</span>
                                            </div>
                                            <div class="menu-item-description text-muted">{{ $item['description'] }}</div>
                                        </div>
                                        <span class="menu-price">{{ $item['price'] }}€</span>
                                    </li>
                                </div>
                                <div class="col-sm-1">
                                    <div class="button-container hide-btn-list" id="btncontain{{ $item['id'] }}">
                                        <button type="button" class="btn btn-dark li-btn-remove rmlibtn"
                                            id="buttonlirem{{ $item['id'] }}"><i class="fa-solid fa-minus"></i></button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

<script>
    // TODO: Refactor this code (it's so bad!!)

    function changeTab(id, view, idrem, viewrem) {
        if (!$("#" + view).hasClass("hide-view visually-hidden")) return;

        $("#" + viewrem).addClass("hide-view visually-hidden");
        $("#" + idrem).removeClass("choosen");
        $("#" + id).addClass("choosen");
        $("#" + view).removeClass("hide-view visually-hidden");
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
                $("#remove" + itemID).attr("disabled", "disabled");
                $("#" + itemID).removeClass("give-space");
                $("#buttons" + itemID).removeClass("show-buttons");
                $("#btncontain" + itemID).addClass("hide-btn-list");
                $("#qntli" + itemID).addClass("visually-hidden");
            }
        })
    }

    $(document).ready(() => {
        $.ajax({
            method: 'post',
            url: '/getCartItems',
            data: {
                "_token": "{{ csrf_token() }}",
                "restaurantID": "{{ $info['id'] }}",
            }
        }).done((res) => {
            if (res == "no items found...") return;
            $.each(res, (key, val) => {
                $("#" + val.id).addClass("give-space");
                $("#buttons" + val.id).addClass("show-buttons");
                $("#qnt" + val.id).text(val.quantity + " no cart");
                $("#item_quantity" + val.id).val(val.quantity);
            })
        })

        $(".menu-item").on('click', function() {
            var item = this.id.replace("li-", "");
            $("#" + item).addClass("give-space");
            $("#buttons" + item).addClass("show-buttons");

            $("#btncontain" + item).removeClass("hide-btn-list");
            $("#qntli" + item).removeClass("visually-hidden");
            cartAddRemove(item);
            $("#item_quantity" + item).val(parseInt($("#item_quantity" + item).val()) + 1);
            $("#qntli" + item).text(" x " + $("#item_quantity" + item).val());

            $("#qnt" + item).text($("#item_quantity" + item).val() + " no cart");
            $("#cart_total").text(parseInt($("#cart_total").text()) + 1);
        })

        $(".rmlibtn").on('click', function() {
            var item = this.id.replace("buttonlirem", "");
            $("#item_quantity" + item).val(parseInt($("#item_quantity" + item).val()) - 1);
            if ($("#item_quantity" + item).val() < 0) {
                $("#item_quantity" + item).val(0);
                return;
            }
            cartAddRemove(item, 1);
            $("#qntli" + item).text(" x " + $("#item_quantity" + item).val());

            $("#qnt" + item).text($("#item_quantity" + item).val() + " no cart");
            $("#cart_total").text(parseInt($("#cart_total").text()) - 1);
        })

        $(".menu_card").on('click', function() {
            $("#btncontain" + this.id).removeClass("hide-btn-list");
            $("#remove" + this.id).removeAttr("disabled", "");
            $("#qntli" + this.id).removeClass("visually-hidden");
            $("#" + this.id).addClass("give-space");
            $("#buttons" + this.id).addClass("show-buttons");
            cartAddRemove(this.id);
            $("#item_quantity" + this.id).val(parseInt($("#item_quantity" + this.id).val()) + 1);
            $("#qnt" + this.id).text($("#item_quantity" + this.id).val() + " no cart");
            $("#qntli" + this.id).text(" x " + $("#item_quantity" + this.id).val());
            $("#cart_total").text(parseInt($("#cart_total").text()) + 1);
        })

        $(".menu-card-ico").on('click', function() {
            if (this.id == "crd-view") {
                changeTab("crd-view", "card_view", "li-view", "list_view");
            } else {
                changeTab("li-view", "list_view", "crd-view", "card_view");
            }
        })

        $(".rem-btns").on('click', function() {
            var item = this.id.replace("remove", ""); // Extract the item id from the btn id
            $("#item_quantity" + item).val(parseInt($("#item_quantity" + item).val()) - 1);
            if ($("#item_quantity" + item).val() < 0) {
                $("#item_quantity" + item).val(0);
                return;
            }
            cartAddRemove(item, 1);
            $("#qnt" + item).text($("#item_quantity" + item).val() + " no cart");
            $("#qntli" + item).text(" x " + $("#item_quantity" + item).val());
            $("#cart_total").text(parseInt($("#cart_total").text()) - 1);
        });
        $(".add-btn").on('click', function() {
            var item = this.id.replace("additem", ""); // Extract the item id from the btn id
            $("#remove" + item).removeAttr("disabled", "");
            cartAddRemove(item);
            $("#item_quantity" + item).val(parseInt($("#item_quantity" + item).val()) + 1);
            $("#qnt" + item).text($("#item_quantity" + item).val() + " no cart");
            $("#qntli" + item).text(" x " + $("#item_quantity" + item).val());
            $("#cart_total").text(parseInt($("#cart_total").text()) + 1);
        });
    });
</script>
