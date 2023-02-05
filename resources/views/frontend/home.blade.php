@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')

    <h1>this is the clients home page!</h1>

    @if (!session()->get('user.isProfessional'))
        <button class="btn btn-primary mt-3" onclick="onSwitchProfessional()">Ativar conta profissional</button>
    @endif

@stop


<script>
    function onSwitchProfessional() {
        window.location.replace("/novo/restaurante");
    }
</script>
