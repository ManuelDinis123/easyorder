@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'orders'])

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<style>
    .item-card {
        width: 505px;
        height: 202px;
        border-radius: 14px;
    }
</style>

@section('content')
    <div class="items-list">
        <div class="pt-2 item-card-content">
            {{-- Creation of food cards --}}
            @foreach ($items as $item)
                <div
                    class="item-card mt-3"style="background-image: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%),url({{ $item['imageUrl'] }}); background-size: cover; background-position: center;">
                    <label class="food-card-label">{{ $item['name'] }}</label>
                    <label class="food-card-quantity"> x {{ $item['quantity'] }}</label>
                    <label class="food-card-price">{{  $item['price']*$item['quantity'] }}€</label>
                </div>
            @endforeach

            <div class="item-card-footer mt-3">
                <label class="total-lbl">Total:</label>
                <label class="total-lbl-price">{{ $total_price }}€</label>
            </div>
        </div>
    </div>
@stop
