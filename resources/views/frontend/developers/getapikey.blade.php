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
                <button class="btn btn-dark form-control" id="generate">Gerar uma API Key</button>
                <div class="center mt-3">
                    <input class="form-control" style="font-size: 24px; background-color:white" id="apiKey" disabled
                        placeholder="*****"></input>
                </div>
                <button onclick="copyToClipboard('apiKey')" class="copy btn btn-dark form-control visually-hidden mt-2"
                    id="clipboard">Copiar</button>
            </div>
        </div>
    </div>

@stop

<script>
    function copyToClipboard(element_id) {
        var aux = document.createElement("div");
        aux.setAttribute("contentEditable", true);
        aux.innerHTML = document.getElementById(element_id).value;
        aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)");
        document.body.appendChild(aux);
        aux.focus();
        document.execCommand("copy");
        document.body.removeChild(aux);
    }

    $(document).ready(() => {
        $("#generate").on('click', () => {
            $.ajax({
                method: "post",
                url: "http://10.0.2.11:3000/dev/generate",
                data: {
                    "email": "{{ session()->get('user.email') }}"
                }
            }).done(res => {
                $("#apiKey").val(res);
                $("#clipboard").removeClass("visually-hidden");
            })
        })
    })
</script>
