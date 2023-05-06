<link rel="stylesheet" href="{{ asset('css/restaurants/restaurant.css') }}">

<style>
    .banner {
        position: absolute;
        top: 0;
        left: 0;
        height: 234px;
        width: 100%;
        filter: drop-shadow(0px 4px 15px rgba(0, 0, 0, 0.25));
        background-size: cover !important;
        background-position: center !important;
        z-index: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 48.44%, #000000ce 100%), {{isset($info['banner']) ? "url(".$info['banner'].")" : "#6e6e6e"}};
    }
</style>

<div class="banner">
    <img src="{{ $info['logo_name'] ? asset('img/logos/' . $info['logo_name']) : $info['logo_url'] }}"
        class="restaurant-logo">
    <h1 class="restaurant-name">{{ $info['name'] }}</h1>
    <div class="desc-container">
        <span class="desc">{{ $info['description'] }}</span>
    </div>
</div>

<div class="restauraunt-content">
    <span class="item {{ $selected == 'main' ? 'item-selected' : '' }}"><a href="/restaurante/{{ $info['id'] }}">Pagina
            Principal</a></span>
    <span class="item {{ $selected == 'publication' ? 'item-selected' : '' }}"><a href="/restaurante/{{ $info['id'] }}/publicacoes">Publicações</a></span>
    <span class="item {{ $selected == 'menu' ? 'item-selected' : '' }}"><a
            href="/restaurante/{{ $info['id'] }}/menu">Ementa</a></span>
    <span class="item {{ $selected == 'reviews' ? 'item-selected' : '' }}"><a href="/restaurante/{{ $info['id'] }}/reviews">Reviews</a></span>
    <hr>
</div>

<input type="hidden" id="restaurant_id" value="{{$info['id']}}">
