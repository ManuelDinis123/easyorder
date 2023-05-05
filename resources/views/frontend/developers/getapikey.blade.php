@include('layouts.includes')

@extends(session()->get('user.isProfessional') ? 'layouts.professional.sidebar' : 'layouts.clients.nav', ['file' => 'options'])

<link rel="stylesheet" href="{{ asset('css/settings/dev.css') }}">

@section('content')
    @include('layouts.professional.tabs', ['tab' => 'dev'])

    <div class="container">
        <div class="center">
            <div class="getApiKey">
                <div class="center">
                    <h1><i class="fa-solid fa-code"></i> Gerar API Key</h1>
                </div>
                <hr>
                <button class="btn btn-dark form-control">Gerar uma API Key</button>
                <div class="center mt-3">
                    <span style="font-size: 24px">*****</span>
                </div>
            </div>
        </div>
    </div>

@stop
