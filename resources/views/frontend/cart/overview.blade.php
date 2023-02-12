@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">

    <div class="overview-header">
        <h1 class="oh-h1">Carinho de Compras <span>{{ $count }} Items</span></h1>
        <hr>
    </div>

    @foreach ($cart as $items)
        @foreach ($items['items'] as $item)
            <div class="item-card-container">
                <div class="item">
                    <img src="{{ $item['imageUrl'] }}" class="item-img">
                    <div class="item-info">
                        <h3>{{ $item['name'] }} <span>x {{ $item['quantity'] }}</span></h3>
                        <span class="total-price">Total: <span>{{ $item['price'] * $item['quantity'] }}€</span></span>
                        <span class="description text-muted">{{ $item['description'] }}</span>
                    </div>
                </div>
            </div>
            <div class="card-btns">
                <button class="btn btn-dark">Deixar Nota</button>
                <button class="btn btn-light">Acompanhamentos</button>
            </div>
        @endforeach
    @endforeach
@stop
