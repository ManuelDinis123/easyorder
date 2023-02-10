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
                    <div class="row">
                        @foreach ($items as $item)
                            <style>
                                #item{{ $item['id'] }} {
                                    background: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%), url("{{ $item['imageUrl'] }}");
                                    background-size: cover;
                                    background-position: center;
                                    border-radius: 31px;
                                }
                            </style>
                            <div class="item-card" id="item{{ $item['id'] }}">
                                <div class="row item-cnts g-0 unselectable">
                                    <div class="col-10">
                                        <span class="item-name unselectable">{{ $item['name'] }}</span>
                                    </div>
                                    <div class="col-2">
                                        <span class="item-price unselectable">{{ $item['price'] + 0 }}€</span>
                                    </div>
                                </div>
                            </div><br>
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

    $(document).ready(() => {
        $(".menu-card-ico").on('click', function() {
            if (this.id == "crd-view") {
                changeTab("crd-view", "card_view", "li-view", "list_view");
            } else {
                changeTab("li-view", "list_view", "crd-view", "card_view");
            }
        })
    });
</script>
