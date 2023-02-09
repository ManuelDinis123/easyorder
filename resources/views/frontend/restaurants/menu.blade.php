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
                        <span class="menu-card-ico"><i class="fa-regular fa-list"></i></span>
                        <span class="menu-card-ico"><i class="fa-regular fa-table"></i></span>
                    </div>
                </div>
            </div>
            <div class="menu-card-body">
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
                            <div class="row item-cnts g-0">
                                <div class="col-10"><span class="item-name">{{ $item['name'] }}</span></div>
                                <div class="col-2"><span class="item-price">{{ $item['price'] + 0 }}€</span></div>
                            </div>
                        </div><br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@stop
