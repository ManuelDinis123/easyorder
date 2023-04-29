@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/settings/user.css') }}">

@section('content')

@if (session()->get('type.owner') || session()->get('type.admin'))
    @include('layouts.professional.tabs', ['tab'=>'users'])
@endif

<div class="user-settings">
    <div class="generalInfo">
        <div class="container">
            {{-- card header --}}
            <div class="row">
                <div class="change-pfp">
                    <form action="/professional/fileupload" class="dropzone" id="profile">
                        @csrf
                        <div class="dz-message" data-dz-message>
                            <img src="{{ asset('img/pfp/' . session()->get('user.pfp')) }}"
                                onerror="this.src = '{{ asset('img/pfp/defaultpfp.png') }}';" alt="profile"
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
                <input type="text" class="form-control settings-inpts mt-2" id="firstName" placeholder="Primeiro Nome"
                    value="{{ session()->get('user.firstName') }}">
                <input type="text" class="form-control settings-inpts mt-4" id="lastName" placeholder="Segundo Nome"
                    value="{{ session()->get('user.lastName') }}">

                <label class="settings-lbls mt-4"><i class="fa-regular fa-envelope"></i> Email:</label>
                <input type="text" class="form-control settings-inpts mt-2" id="email" placeholder="Email"
                    value="{{ session()->get('user.email') }}">

                <label class="settings-lbls mt-4"><i class="fa-regular fa-calendar"></i> Data de Nascimento:</label>
                <input type="text" class="form-control settings-inpts mt-º2" placeholder="Data de Nascimento"
                    value="{{ date('d/m/Y', strtotime(session()->get('user.birthdate'))) }}" id="birthdate"
                    data-provide="datepicker">

                <label class="settings-lbls mt-3"><i class="fa-regular fa-key"></i> Mudar Password:</label>
                <input type="password" class="form-control settings-inpts mt-2" placeholder="Password Antiga"
                    id="oldPsw">
                <input type="password" class="form-control settings-inpts mt-4" placeholder="Password Nova" id="newPsw">

                <div class="btn-container mt-3">
                    <button class="btn btn-primary mt-3 settings-btns" id="saveChanges">Guardar</button>
                    <button class="btn btn-secondary mt-3 settings-btns" onclick="reset()">Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@stop

<script>
    // Dropzone configuration
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

    // Reset the user settings
    function reset() {
        $("#firstName").val("{{ session()->get('user.firstName') }}");
        $("#lastName").val("{{ session()->get('user.lastName') }}");
        $("#birthdate").val("{{ date('d/m/Y', strtotime(session()->get('user.birthdate'))) }}");
        $("#email").val("{{ session()->get('user.email') }}");

        $("#oldPsw").val("");
        $("#newPsw").val("");
    }

    $(document).ready(() => {
        $("#birthdate").datepicker({
            format: 'dd/mm/yyyy'
        });

        $("#saveChanges").on('click', () => {
            map = ["firstName", "lastName", "birthdate", "email"];

            invalid = animateErr(map);
            if (invalid) {
                return;
            }

            // Check if has to update any settings
            inputValuesMap = [
                $("#firstName").val(),
                $("#lastName").val(),
                $("#birthdate").val(),
                $("#email").val(),
            ];
            sessionValuesMap = [
                "{{ session()->get('user.firstName') }}",
                "{{ session()->get('user.lastName') }}",
                "{{ date('d/m/Y', strtotime(session()->get('user.birthdate'))) }}",
                "{{ session()->get('user.email') }}"
            ];
            hasToUpdate = false;
            $.each(inputValuesMap, (key, val) => {
                if (val != sessionValuesMap[key]) {
                    hasToUpdate = true;
                    return false;
                }
            })

            hasToUpdatePassword = ($("#oldPsw").val() != "" || $("#newPsw").val() != "") ? 1 : 0;

            if (!hasToUpdate && !hasToUpdatePassword) {
                return;
            }

            $.ajax({
                    method: 'post',
                    url: '/professional/updateusersettings',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'update': hasToUpdate,
                        'updatePsw': hasToUpdatePassword,
                        'values': inputValuesMap,
                        'passwords': {
                            'oldPsw': $("#oldPsw").val(),
                            'newPsw': $("#newPsw").val()
                        },
                    }
                })
                .done((res) => {
                    successToast(res.status, res.message);
                }).fail((err)=>{
                    errorToast(err.responseJSON.status, err.responseJSON.message);
                })

        })
    });
</script>