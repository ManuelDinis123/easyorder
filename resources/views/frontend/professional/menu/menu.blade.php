@include('layouts.includes')
<!-- Create New Modal -->
<div class="modal fade" id="addModal" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel"><i class="fa-solid fa-circle-plus text-icon"></i> Adicionar Novo
                    Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="mt-1">Nome:</label>
                <input type="text" id="title" class="form-control" placeholder="Nome do item">

                <label class="mt-3">Preço:</label>
                <input type="number" id="price" class="form-control" placeholder="Preço €">

                <label class="mt-3">Custo:</label>
                <input type="number" id="cost" class="form-control" placeholder="Custo de produção €">

                <label class="mt-3">Descrição:</label>
                <textarea type="text" id="description" class="form-control" placeholder="Descrição sobre o item"></textarea>

                <label class="mt-3">Etiquetas:</label><br />
                <span class="text-muted" style="font-size: 15px">(optional)</span><br />
                <input id="tags" class='customLook'>
                <button type="button" id="tag_more">+</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="save">Guardar</button>
                <button type="button" class="btn btn-primary">Guardar e abrir</button>
            </div>
        </div>
    </div>
</div>
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/menu.css') }}">



@section('content')
    <div class="container-fluid" style="padding-top:15px">
        <div class="centered">
            <div class="c-contents">

                {{-- Header --}}
                <div class="row">
                    <div class="col-6">
                        <h3 class="c-h">Ementa</h3>
                    </div>
                    <div class="col-6">
                        <span class="icons" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                class="fa-solid fa-plus"></i></span>
                    </div>
                </div>

                <hr style="height: 2px;" class="separation-line">

                {{-- Table --}}
                <table id="menu" class="table table-borderless">
                    <thead>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th></th>
                    </thead>
                </table>


            </div>
        </div>
    </div>
@stop

<script src="{{ asset('js/menu.js') }}"></script>
