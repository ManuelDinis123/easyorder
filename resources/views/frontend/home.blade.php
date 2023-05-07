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
                            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 31.77%, #000000 100%), url("{{ isset($o['imageUrl']) ? $o['imageUrl'] : 'https://trello.com/1/cards/642f03e28350900aa3aac4ee/attachments/6430690d990221cd112dbc0f/download/image.png' }}");
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
            @if (count($gallery) > 0)
                <style>
                    .showcase {
                        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 91.67%, #000000 94.27%),
                            url("{{ $gallery[0]['imageUrl'] }}");
                        border-radius: 42px;
                        width: 98%;
                        height: 801px;
                        background-size: cover;
                        background-position: center;
                    }
                </style>
                <div class="col-lg-6 col-md-12 col-sm-12">
                    <div class="showcase" id="showcase_imgs">
                        <i class="fa-sharp fa-solid fa-stars"></i>
                        <div class="balls-container">
                            @foreach ($gallery as $key => $g)
                                <div class="ball {{ $key == 0 ? 'selected' : '' }}" id="img{{ $g['card_num'] }}"></div>
                            @endforeach
                            <input type="hidden" id="galleryImgs" value="{{ json_encode($gallery) }}">
                        </div>
                    </div>
                </div>
            @endif
            @if ($showcase)
                @if (count($gallery) <= 0)
                    <style>
                        .showcase-description {
                            width: 50% !important;
                        }
                    </style>
                @endif
                <div class="{{ count($gallery) > 0 ? 'col-lg-6' : 'col-lg-12 center' }} col-md-12 col-sm-12">
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
                        <button class="btn btn-dark form-control mt-5"
                            onclick="window.location.href='/restaurante/{{ $showcase['id'] }}'">Ver Página</button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        let selected = 1;

        let allimgs = "";
        let slider = ["", "", "", "", ""];
        if ($("#galleryImgs").val() != undefined) {
            allimgs = JSON.parse($("#galleryImgs").val());
            $.each(allimgs, (key, val) => {
                slider[val.card_num - 1] = val.imageUrl;
            })
        }

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
                $(this).attr("src", (menuImgs[i]['imageUrl'] != null ? menuImgs[i]['imageUrl'] :
                    'https://trello.com/1/cards/642f03e28350900aa3aac4ee/attachments/6430690d990221cd112dbc0f/download/image.png'
                ));
                $(this).fadeIn(1150);
            });
        }, 5000);

        function clickCard(id) {
            window.location.href = "/restaurante/" + id + "/menu";
        }
    </script>

@stop
