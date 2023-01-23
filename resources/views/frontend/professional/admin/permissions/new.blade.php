@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'perms'])

<link rel="stylesheet" href="{{ asset('css/perms.css') }}">

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-3 g-0">
                <div class="new-card">
                    <h4 style="font-weight: 600;">Nome:</h4>
                    <input type="text" class="form-control" placeholder="Nome do Tipo" autocomplete="off">
                    <button class="btn btn-primary form-control mt-3">Criar</button>
                </div>
            </div>
            <div class="col-6">
                <div class="checkboxes-card">
                    <h4 style="font-weight: 600">Permissões:</h4>
                    <hr class="mt-2">
                    @php $map=["Ver Pedidos", "Editar Pedidos", "Ver Menu", "Editar Menu", "Ver Estatisticas", "Convidar Utilizadores", "Banir Utilizadores", "Admin"] @endphp
                    <div class="row">
                        @foreach ($map as $permission)
                            <div class="col-12">
                                <label class="perm-label">{{ $permission }}</label>
                                <div class="form-check form-switch perm-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        id="flexSwitchCheckDefault">
                                </div>
                            </div>
                            <hr class="mt-3">
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
