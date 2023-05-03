@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')

    <style>
        .card {
            background: rgb(39, 39, 39);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3) !important;
            padding: 25px 25px;
            border-radius: 15px;
        }
    </style>
    <div style="display: flex; justify-content: center;">
        <div class="card">
            <h1 style="color: rgb(231, 231, 231); font-weight:800">Pedido Efetuado com Sucesso!</h1>
        </div>
    </div>

    <script>
        $.ajax({
            method: "post",
            url: "/createorder",
            data: {
                "_token": "{{ csrf_token() }}",
                "deadline": "{{$deadline}}"
            }
        }).done(res => {
            successToast(res.title, res.message);
        }).fail(err => {
            errorToast(err.responseJSON.title, err.responseJSON.message);
        })
    </script>

@stop
