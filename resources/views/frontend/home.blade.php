@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

    <div class="center">
        <div class="orders-today">
            <h1>Mais Encomendados Hoje</h1>
        </div>
    </div>
    <div class="scroll">
        @if (count($orders) > 0)
            <div class="scrollable">
                @foreach ($orders as $o)
                    <style>
                        .card{{ $o['id'] }} {
                            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 31.77%, #000000 100%), url("{{ $o['imageUrl'] }}");
                            background-size: cover;
                            background-position: center;
                            border-radius: 14px;
                            width: 300px;
                            height: 300px;
                            margin: 5px 5px;
                            padding: 10px 10px;
                        }
                    </style>
                    <div class="card{{ $o['id'] }} unselectable allCards" onclick="clickCard({{ $o['restaurantID'] }})">
                        <div class="card-label">
                            <div class="row">
                                <div class="col-8">
                                    <span>
                                        <label class="bold">{{ $o['name'] }}</label>
                                        <br>
                                        {{ $o['restaurant_name'] }}
                                    </span>
                                </div>
                                <div class="col-4">
                                    <br>
                                    <span class="bold" style="float:right;">{{ $o['price'] + 0 }}€</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="center">
                <span class="text-muted">Não houve encomendas hoje</span>
            </div>
        @endif
    </div>
    <div class="best-rating-container mt-4">
        <div class="center">
            <h1 style="font-weight: 600">Melhor Rating</h1>
        </div>
        <div class="row g-0">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="showcase" id="showcase_imgs">
                    <i class="fa-sharp fa-solid fa-stars"></i>
                    <div class="balls-container">
                        <div class="ball" id="img1"></div>
                        <div class="ball" id="img2"></div>
                        <div class="ball selected" id="img3"></div>
                        <div class="ball" id="img4"></div>
                        <div class="ball" id="img5"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="showcase-description">
                    <div class="center">
                        <h2>{{ $showcase['name'] }}</h2>
                    </div>
                    <div class="center">
                        <hr style="width: 70%">
                    </div>
                    <span class="center">{{ $showcase['description'] }}</span><br>
                    <div class="center mt-2">
                        <img class="menu-showcase" id="menuShow" src="{{ $menuImgs[0]['imageUrl'] }}"><br>
                    </div>
                    <button class="btn btn-dark form-control mt-5" onclick="window.location.href='/restaurante/{{$showcase['id']}}'">Ver Página</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selected = 3;

        let slider = [
            "https://www.confeiteiradesucesso.com/wp-content/uploads/2019/03/hamburguergourmet-fb.jpg",
            "https://t3.ftcdn.net/jpg/03/24/73/92/360_F_324739203_keeq8udvv0P2h1MLYJ0GLSlTBagoXS48.jpg",
            "https://assets.vogue.com/photos/605b998c1087bb7115b724b5/16:9/w_1600,h_900,c_limit/Sona_Interiors_048.jpg",
            "https://images.pexels.com/photos/1126728/pexels-photo-1126728.jpeg?cs=srgb&dl=pexels-lisa-fotios-1126728.jpg&fm=jpg",
            "https://images.unsplash.com/photo-1620878439129-177b2d18864c?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxzZWFyY2h8Mnx8cmVzdGF1cmFudCUyMGJhY2tncm91bmR8ZW58MHx8MHx8&w=1000&q=80",
        ]

        $(document).on('click', '.ball', function() {
            var imageToShow = this.id.replace("img", "");
            $("#showcase_imgs").css("background",
                "linear-gradient(180deg, rgba(0, 0, 0, 0) 91.67%, #000000 94.27%),url(" + slider[imageToShow -
                    1] + ")");
            $("#showcase_imgs").css("background-size", "cover ");
            $("#showcase_imgs").css("background-position", "center ");

            $("#img" + imageToShow).addClass("selected");
            $("#img" + selected).removeClass("selected");
            selected = imageToShow;
        });

        // Menu showcase
        let selectedImg = 0;
        const menuImgs = JSON.parse(`{!! json_encode($menuImgs) !!}`);

        var pic = $("#menuShow");
        var i = 0;
        setInterval(function() {
            i = (i + 1) % menuImgs.length;
            pic.fadeOut(350, function() {
                $(this).attr("src", menuImgs[i]['imageUrl']);
                $(this).fadeIn(1150);
            });
        }, 5000);

        function clickCard(id) {
            window.location.href = "/restaurante/" + id + "/menu";
        }
    </script>

@stop
