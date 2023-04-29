@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/settings/main.css') }}">

@section('content')

@php
    $usersList = "";
    foreach ($users as $key => $value) {
        $usersList .= "<div class='select-user'><i class=\"fa-solid fa-caret-right\"></i> ".$value->first_name." " . $value->last_name . "</div><hr>";
    }
@endphp

@component("components.modal_builder", [
    'modal_id' => "allUsers",
    'hasHeader' => true,
    'rawHeader' => '<h5 class="modal-title" id="allUsersLabel"><i class="fa-solid fa-crown" style="color: #d6c400;"></i> Escolher novo owner</h5>',
    'hasBody' => true,
    'rawBody' => $usersList,
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
        ['label' => 'Guardar', 'id' => 'Confirmar', 'class' => 'btn btn-primary'],
    ]
])
@endcomponent

@include('layouts.professional.tabs', ['tab'=>'general'])

<div class="container">
    <div class="row">
        <div class="col-3"></div>
        <div class="col-9">
            <div class="user-activity">
                <div class="center">
                    <h1>Atividades</h1>
                </div>
                <hr>
                <div class="list unselectable">
                    <div class="item">
                        <h5>Username</h5>
                        <span>Ação realizada por esse user</span>
                    </div>
                    <hr>
                    <div class="item">
                        <h5>Username</h5>
                        <span>Ação realizada por esse user</span>
                    </div>
                    <hr>
                    <div class="item">
                        <h5>Username</h5>
                        <span>Ação realizada por esse user</span>
                    </div>
                    <hr>
                </div>
            </div><br>
            <hr>
            <div class="ownership mt-5">
                <button class="btn btn-primary form-control" data-bs-toggle="modal" data-bs-target="#allUsers">Transferir Ownership</button>
            </div>
        </div>
    </div>
</div>

@stop