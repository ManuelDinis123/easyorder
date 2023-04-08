@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    @include('components.frontend.restaurantstop', ['selected' => 'main'])

    @if ($plateofday)
        <div class="prato-dia mt-5">
            <div class="center">
                <h1 class="title">Prato do Dia</h1>
            </div>
            <div class="center">
                <style>
                    .pd-img {
                        padding: 20px 20px;
                        background-size: cover;
                        background-position: center;
                        background-image: linear-gradient(180deg,
                                rgba(0, 0, 0, 0) 64.06%,
                                #000000 100%),
                            url("{{ $plateofday['imageUrl'] }}");
                        width: 90%;
                        height: 553px;
                        border-radius: 10px;
                    }
                </style>
                <div class="pd-img">
                    <div class="row pd-itm-cn g-0 unselectable">
                        <div class="col-8">
                            <span class="unselectable pd-itm">{{ $plateofday['name'] }}</span><br>
                            <span class="unselectable pd-itm"
                                style="font-weight: 300; font-size: 30px">{{ $plateofday['description'] }}</span>
                        </div>
                        <div class="col-4">
                            <span class="unselectable pd-itm" style="float: right">{{ $plateofday['price'] + 0 }}€</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="center">
                <button class="btn btn-dark pd-btn">Adicionar ao Carrinho</button>
            </div>
        </div>
    @endif

    <div class="popular-plates mt-5">
        <div>
            <h1 class="title">Populares</h1>
        </div>
        <div class="slider">
            <div class="scrollable">
                @foreach ($popular as $item)
                    <style>
                        .pcard{{ $item['id'] }} {
                            background-image: linear-gradient(180deg,
                                    rgba(0, 0, 0, 0) 31.77%,
                                    #000000 100%),
                                url("{{ $item['imageUrl'] }}");
                            background-size: cover;
                            background-position: center;
                        }
                    </style>
                    <div class="p-card pcard{{ $item['id'] }}">
                        <div class="price-container">
                            <h3>{{ $item['price'] }}€</h3>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="center">
            <button class="btn btn-dark pd-btn" style="width: 100%"
                onclick="window.location.replace('/restaurante/{{ $info['id'] }}/menu')">Ver Ementa Completa</button>
        </div>
    </div>

    <script>
        // Make most popular section scrollabe with the mouse
        const scrollable = $('.scrollable');
        let isDown = false;
        let startX;
        let scrollLeft;

        scrollable.on('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - scrollable.offset().left;
            scrollLeft = scrollable.scrollLeft();
            scrollable.css('cursor', 'grabbing');
        });

        scrollable.on('mouseleave', () => {
            isDown = false;
            scrollable.css('cursor', 'default');
        });

        scrollable.on('mouseup', () => {
            isDown = false;
            scrollable.css('cursor', 'default');
        });

        scrollable.on('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollable.offset().left;
            const walk = x - startX;
            scrollable.scrollLeft(scrollLeft - walk);
        });
    </script>


@stop
