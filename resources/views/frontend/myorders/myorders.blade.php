@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')

    <link rel="stylesheet" href="{{ asset('css/myorders.css') }}">

    <div class="center">
        <button class="btn btn-dark" style="margin-right: 5px" onclick="changeTab('pending')">Pendentes</button>
        <button class="btn btn-dark" style="margin-right: 5px" onclick="changeTab('done')">Finalizados</button>
        <button class="btn btn-dark" style="margin-right: 5px" onclick="changeTab('cancelled')">Cancelados</button>
    </div>
    <div class="center mt-3">
        <div class="orders-card" id="pending">
            @if (count($orders) == 0)
                <span class="text-muted">Parece que não tem encomendas!</span>
            @endif
            @foreach ($orders as $o)
                <div class="order">
                    <div class="order-header">
                        <span style="float: left">{{ $o['deadline'] }}</span>
                        <span style="float: right">{{ $o['restaurant'] }}</span>
                    </div>
                    <br />
                    <div class="order-body mt-2">
                        <span>{{ $o['items'] }}</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $o['progress'] }}%; background-color: #0c0c0c" aria-label="Basic example"
                            aria-valuenow="{{ $o['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
        <div class="orders-card visually-hidden" id="done">
            @if (count($closed) == 0)
                <span class="text-muted">Parece que não tem encomendas!</span>
            @endif
            @foreach ($closed as $o)
                <div class="order">
                    <div class="order-header">
                        <span style="float: left">{{ $o['deadline'] }}</span>
                        <span style="float: right">{{ $o['restaurant'] }}</span>
                    </div>
                    <br />
                    <div class="order-body mt-2">
                        <span>{{ $o['items'] }}</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $o['progress'] }}%; background-color: #0c0c0c" aria-label="Basic example"
                            aria-valuenow="{{ $o['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
        <div class="orders-card visually-hidden" id="cancelled">
            @if (count($cancelled) == 0)
                <span class="text-muted">Parece que não tem encomendas!</span>
            @endif
            @foreach ($cancelled as $o)
                <div class="order">
                    <div class="order-header">
                        <span style="float: left">{{ $o['deadline'] }}</span>
                        <span style="float: right">{{ $o['restaurant'] }}</span>
                    </div>
                    <br />
                    <div class="order-body mt-2">
                        <span>{{ $o['items'] }}</span>
                    </div>
                    <div class="progress mt-2">
                        <div class="progress-bar" role="progressbar"
                            style="width: {{ $o['progress'] }}%; background-color: #0c0c0c" aria-label="Basic example"
                            aria-valuenow="{{ $o['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function changeTab(target) {
            if ($("#" + target).hasClass("visually-hidden")) {
                $("#" + target).removeClass("visually-hidden");
                const map = ["pending", "done", "cancelled"];
                $.each(map, (key, val) => {
                    if (val != target) {
                        !$("#" + val).hasClass("visually-hidden") ?
                            $("#" + val).addClass("visually-hidden") : null;
                    }
                })
            }
        }
    </script>

@stop
