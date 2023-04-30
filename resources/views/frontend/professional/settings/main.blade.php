@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'options'])

<link rel="stylesheet" href="{{ asset('css/settings/main.css') }}">

@section('content')

    @php
        $usersList = '';
        foreach ($users as $key => $value) {
            $usersList .= "<div class='select-user unselectable' onclick='selectOwner(" . $value->id . ")'><i class=\"fa-solid fa-caret-right\"></i> " . $value->first_name . " " . $value->last_name . "</div><hr>";
        }
    @endphp

    @component('components.modal_builder', [
        'modal_id' => 'allUsers',
        'hasHeader' => true,
        'rawHeader' =>
            '<h5 class="modal-title" id="allUsersLabel"><i class="fa-solid fa-crown" style="color: #d6c400;"></i> Escolher novo owner</h5>',
        'hasBody' => true,
        'rawBody' => $usersList,
        'hasFooter' => true,
        'buttons' => [['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true]],
    ])
    @endcomponent

    @component('components.modal_builder', [
        'modal_id' => 'confirmTransfer',
        'hasHeader' => true,
        'rawHeader' =>
            '<h5 class="modal-title" id="allUsersLabel"><i class="fa-solid fa-circle-exclamation" style="color: #901818;"></i> Tem a certeza?</h5>',
        'hasBody' => true,
        'rawBody' =>
            '<span class="text-muted">Não pode reverter esta ação</span><br><h4>Digite "' .
            session()->get('restaurant.name') .
            '" para continuar</h4><input class="form-control" type="text" id="confirmName"><input type="hidden" id="idOfOwner">',
        'hasFooter' => true,
        'buttons' => [
            ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-dark', 'dismiss' => true],
            ['label' => 'Confirmar', 'id' => 'confirmAction', 'class' => 'btn btn-danger'],
        ],
    ])
    @endcomponent

    @include('layouts.professional.tabs', ['tab' => 'general'])

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
                @if (session()->get('type.owner'))
                    <div class="ownership mt-5">
                        <button class="btn btn-primary form-control" data-bs-toggle="modal"
                            data-bs-target="#allUsers">Transferir Ownership</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Selects a new owner
        function selectOwner(userID) {
            $("#allUsers").modal("toggle");
            $("#idOfOwner").val(userID);
            $("#confirmTransfer").modal("toggle");
        }

        $("#confirmAction").on('click', () => {
            if($("#confirmName").val()!="{!!session()->get('restaurant.name')!!}"){
                errorToast("Erro", "nome incorreto");
                return;
            }

            $.ajax({
                method: 'post',
                url: '/professional/configuracoes/novoowner',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "newOwner": $("#idOfOwner").val()
                }
            }).done((res) => {
                successToast(res.status, res.message);
                window.location.href="/";
            }).fail((err) => {
                errorToast(err.responseJSON.status, err.responseJSON.message)
            })
        });
    </script>

@stop
