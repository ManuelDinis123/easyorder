@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/settings/user.css') }}">

@section('content')
    <div class="user-settings">
        <div class="generalInfo">
            <div class="container">
                {{-- card header --}}
                <div class="row">
                    <div class="change-pfp">
                        <form action="/professional/fileupload" class="dropzone" id="profile">
                            @csrf
                            <div class="dz-message" data-dz-message>
                                <img src="{{ asset('img/pfp/' . session()->get('user.pfp')) }}" onerror="this.src = '{{asset('img/pfp/defaultpfp.png')}}';" alt="profile"
                                    class="pfp_settings" id="pfp" />
                                <i class="fa-solid fa-camera cam"></i>
                            </div>
                            <br />
                        </form>
                    </div>
                    <h3 class="settings-name mt-1">
                        {{ session()->get('user.firstName') . ' ' . session()->get('user.lastName') }}</h3>

                    <hr>

                </div>
                {{-- card body --}}
                <div class="row">
                    <label class="settings-lbls"><i class="fa-regular fa-user"></i> Nome:</label>
                    <input type="text" class="form-control settings-inpts mt-2" placeholder="Primeiro Nome"
                        value="{{ session()->get('user.firstName') }}">
                    <input type="text" class="form-control settings-inpts mt-4" placeholder="Segundo Nome"
                        value="{{ session()->get('user.lastName') }}">

                    <label class="settings-lbls mt-4"><i class="fa-regular fa-envelope"></i> Email:</label>
                    <input type="text" class="form-control settings-inpts mt-2" placeholder="Email"
                        value="{{ session()->get('user.email') }}">

                    <label class="settings-lbls mt-4"><i class="fa-regular fa-calendar"></i> Data de Nascimento:</label>
                    <input type="text" class="form-control settings-inpts mt-2" placeholder="Data de Nascimento"
                        value="{{ date('d/m/Y', strtotime(session()->get('user.birthdate'))) }}" data-provide="datepicker"
                        id="dob">

                    <label class="settings-lbls mt-3"><i class="fa-regular fa-key"></i> Mudar Password:</label>
                    <input type="password" class="form-control settings-inpts mt-2" placeholder="Password Antiga">
                    <input type="password" class="form-control settings-inpts mt-4" placeholder="Password Nova">

                    <div class="btn-container mt-3">
                        <button class="btn btn-primary mt-3 settings-btns">Guardar</button>
                        <button class="btn btn-secondary mt-3 settings-btns">Reset</button>
                    </div>
                </div>

                {{-- <hr class="mt-4"> --}}




            </div>
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
        success: function(file, response) {
            $("#pfp").attr('src', `{{ asset('img/pfp') }}/${response.success}`);
            $("#userIco").attr('src', `{{ asset('img/pfp') }}/${response.success}`);
            this.removeAllFiles(true);
        }
    }

    $(document).ready(() => {
        $("#dob").datepicker({
            format: 'dd/mm/yyyy'
        })
    });
</script>
