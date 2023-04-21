@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@extends('layouts.admin.sidebar', ['file' => 'dashboard'])

@section('content')

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="dashboard-cards">
                <h3 class="center">Users</h3>
                <hr>
                <span style="font-size: 20px" class="center">{{ $users }} Users no total</span>
                <button class="btn btn-dark form-control mt-3" onclick="window.location.href='/admin/users'">Ver
                    Users</button>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="dashboard-cards">
                <h3 class="center">Restaurantes</h3>
                <hr>
                <span style="font-size: 20px" class="center">{{ $rest }} restaurantes no total</span>
                <button class="btn btn-dark form-control mt-3" onclick="window.location.href='/admin/restaurantes'">Ver
                    Restaurantes</button>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12">
            <div class="dashboard-cards">
                <h3 class="center">Denúncias</h3>
                <hr>
                <span style="font-size: 20px" class="center">{{ $den }} denúncias no total</span>
                <button class="btn btn-dark form-control mt-3" onclick="window.location.href='/admin/denuncias'">Ver
                    Denúncias</button>
            </div>
        </div>
    </div>

@stop
