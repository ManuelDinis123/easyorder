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
                                    background: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%), url("{{ $item['imageUrl'] }}");
                                    background-size: cover;
                                    background-position: center;
                                    border-radius: 31px;
                                    z-index: 2;
                                }
                            </style>
                            <div class="item-card">
                                <div id="{{ $item['id'] }}" class="item{{ $item['id'] }} menu_card"
                                    style="padding:0px 10px; margin-bottom:24px;">
                                    <div class="row item-cnts g-0 unselectable">
                                        <div class="col-10">
                                            <span class="item-name unselectable">{{ $item['name'] }}</span>
                                        </div>
                                        <div class="col-2">
                                            <span class="item-price unselectable">{{ $item['price'] + 0 }}€</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 buttons-contain" id="buttons{{ $item['id'] }}">
                                    <span>
                                        <button class="btn btn-num cart-btns rem-btns"
                                            id="remove{{ $item['id'] }}">-</button>
                                    </span>
                                    <span>
                                        <button class="btn btn-mid cart-btns">1 no cart</button>
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
                            <li class="menu-item">
                                <div class="menu-item-info">
                                    <div class="menu-item-name">{{ $item['name'] }}</div>
                                    <div class="menu-item-description text-muted">{{ $item['description'] }}</div>
                                </div>
                                <span class="menu-price">{{ $item['price'] }}€</span>
                            </li>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

<script>
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
            console.log(res);
            if (isRemove && res == "deleted") {
                $("#" + itemID).removeClass("give-space");
                $("#buttons" + itemID).removeClass("show-buttons");
            }
        })
    }

    $(document).ready(() => {
        $(".menu_card").on('click', function() {
            $("#" + this.id).addClass("give-space");
            $("#buttons" + this.id).addClass("show-buttons");
            cartAddRemove(this.id);
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
            cartAddRemove(item, 1);
        });
        $(".add-btn").on('click', function() {
            var item = this.id.replace("additem", ""); // Extract the item id from the btn id
            cartAddRemove(item);
        });
    });
</script>
