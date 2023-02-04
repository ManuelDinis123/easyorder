@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'edit_users'])

<link rel="stylesheet" href="{{ asset('css/admin_users.css') }}">

@section('content')

    <div class="container">
        <div class="details-card">
            <div class="row g-0">
                <div class="col-1">
                    <img src="{{ asset('img/pfp/' . $pfp) }}" class="details-pfp">
                </div>
                <div class="col-3">
                    <h3 class="mt-4 details-username">{{ $name }}</h3>
                    <span class="text-muted details-db">{{ $birthdate }}</span>
                </div>
                <div class="col-8">
                    <div style="margin-top: 38px;">
                        <span class="dtypes">Tipo:</span><br>
                        <h4 class="details-type dtypes">{{ $label }}</h4>
                    </div>
                </div>
            </div>
            <hr>
            <div class="details-body">
                <div class="row g-0">
                    <div class="col-5">
                        <label>Nome:</label>
                        <input type="text" value="{{ $name }}" class="form-control" disabled>
                        <label class="mt-3">Data de nascimento:</label>
                        <input type="text" value="{{ $birthdate }}" class="form-control" disabled>
                        <label class="mt-3">Email:</label>
                        <input type="text" value="{{ $email }}" class="form-control" disabled>
                        <hr style="margin-top: 30px">
                        <label class="mt-2">Tipo:</label>
                        <select id="user_type" class="form-select"«>
                            <option value="0" disabled>Selecione um Tipo</option>
                            @foreach ($types as $type)
                                <option value="{{ $type['id'] }}" {{ $type['id'] == $typeID ? 'selected' : '' }}>
                                    {{ $type['label'] }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-primary mt-3" style="width: 100%">Editar Tipo</button>
                    </div>
                    <div class="col-7">
                        <div class="access-description">
                            <h5 class="fw-bolder">Este utilizador tem acesso a:</h5>
                            <div class="description">
                                <ul class="list-group list-group-numbered pm-list">
                                    @foreach ($permissions_description as $text)
                                        @if ($text != '')
                                            <li class="list-group-item">{{ $text }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                                <button class="btn btn-primary mt-3 pm-list" onclick="editPermissions({{$typeID}})"
                                    {{ $isOwner || ($isAdmin && !session()->get('type.owner')) ? 'disabled' : '' }}>Editar
                                    estas permissões</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop


<script>
    function editPermissions(id){
        window.location.href = "/professional/admin/permissions/"+id;
    }
</script>