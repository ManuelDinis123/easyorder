@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'options'])

<link rel="stylesheet" href="{{ asset('css/settings/main.css') }}">

@section('content')

    @include('layouts.professional.tabs', ['tab' => 'general'])

    <div class="container">
        <div class="settings-content">
            <div class="page-card">
                <div class="banner"></div>
                <div class="restaurant-logo"></div>
                <input type="text" id="restaurant_name" class="form-control mt-1 general-inputs"
                    placeholder="Nome do restaurante">
                <textarea id="desc" class="form-control mt-1 general-inputs textareawidth" rows="5"
                    placeholder="Descrição do restaurante"></textarea>
                <button class="btn btn-primary mt-3 mx-2" style="float: right">Guardar</button>
                <button class="btn btn-danger mt-3" style="float: right">Cancelar</button>
            </div>
        </div>
    </div>

@stop
