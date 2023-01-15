@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/settings/user.css') }}">

@section('content')

    <div class="user-settings">
        <label>Mudar Foto</label>
        <div class="change-pfp">
            <img src="{{ asset('img/pfp/' . session()->get('user.pfp')) }}" alt="profile" class="pfp_settings" id="pfp">
        </div><br />
        <div class="row" style="display: flex; justify-content: center">
            <form action="/professional/fileupload" class="dropzone" id="profile">
                @csrf
                <div class="dz-message" data-dz-message>
                    <span>Mudar Foto de perfil</span>
                </div>
            </form>
            <div class="col-5">
                <label>Nome:</label>
                <input type="text" class="form-control" placeholder="Primeiro Nome">
                <input type="text" class="form-control mt-4" placeholder="Segundo Nome">
            </div>
        </div>
    </div>

@stop

<script>    
    Dropzone.options.profile = {
        method: 'post',        
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        uploadMultiple: false,
        maxFiles: 1,
        success: function (file, response) {
            $("#pfp").attr('src', `{{ asset('img/pfp') }}/${response.success}`);
            $("#userIco").attr('src', `{{ asset('img/pfp') }}/${response.success}`);
        }
    }
</script>
