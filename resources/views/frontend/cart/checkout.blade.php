@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')

    <h1 style="display: flex; justify-content: center;">Pedido Efetuado com Sucesso!</h1>    

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
