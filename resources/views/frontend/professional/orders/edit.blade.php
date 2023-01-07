@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'orders'])

<link rel="stylesheet" href="{{ asset('css/orders.css') }}">

<style>
    .card-contain {
        width: 505px;
        height: 202px;
        border-radius: 14px;
        cursor: pointer;
        overflow: hidden
    }

    .item-card {                
        border-radius: 14px;
        background-size: 100%;
        background-position: center;
        -webkit-transition: all .4s;
        transition: all .4s ease-in-out;
    }

    .item-card:hover {
        background-size: 105%;
    }

    .item-gradient {
        opacity: 1;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%);
        -webkit-transition: all .4s;
        transition: all .4s ease-in-out;
    }

    .item-gradient:hover {
        opacity: .7;
    }
</style>

<div class="modal fade" id="ingredientsModal" aria-labelledby="ingredientsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <h4>Acompanhamentos:</h4>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Sair</button>
            </div>
        </div>
    </div>
</div>

@section('content')
    <div class="items-list">
        <div class="pt-2 item-card-content">
            <div class="scroll-card">
                {{-- Creation of food cards --}}
                @foreach ($items as $item)
                    <div class="card-contain mt-3">
                        <div class="item-card" style="background-image: url({{ $item['imageUrl'] }});">
                            <div class="item-gradient">
                                <label class="food-card-label">{{ $item['name'] }}</label>
                                <label class="food-card-quantity"> x {{ $item['quantity'] }}</label>
                                <label class="food-card-price">{{ $item['price'] * $item['quantity'] }}€</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="item-card-footer mt-3">
                <label class="total-lbl">Total:</label>
                <label class="total-lbl-price">{{ $total_price }}€</label>
            </div>
        </div>
    </div>
@stop
