@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'dashboard'])

<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@section('content')
    @if (session()->get('restaurant.isPublic'))
        <p>this is the dashboard page of the professional accounts</p>
    @else
        <div class="row">
            <div class="col-6">
                <div class="general-set-up">
                    <h3 style="font-weight: 700">Informação Geral</h3>
                    <hr>
                    <label>Nome:</label>
                    <input type="text" class="form-control" id="name" autocomplete="off" placeholder="nome"
                        value="{{ session()->get('restaurant.name') }}">
                    <label class="mt-3">Descrição:</label>
                    <textarea class="form-control" id="description" rows="1" placeholder="Sobre o seu restaurante"></textarea>
                    <label class="mt-3">Logo:</label>
                    <div class="row g-0">
                        <div class="col-11">
                            <input type="text" class="form-control" placeholder="URL ou Upload" id="imageUrl"
                                autocomplete="off">
                        </div>
                        <div class="col-1">
                            <label for="imgFile" class="filePut">
                                <img id="showLogo" alt="logo" class="logoRestaurant"></img>
                                <input type="file" id="imgFile" style="display: none" accept="image/*">
                            </label>
                            <input type="hidden" id="hasFile" value="0">
                        </div>
                    </div>
                    <button class="btn btn-primary mt-4" id="saveGenInfo">Guardar</button>
                </div>
            </div>
            <div class="col-6">
                <div class="other-set-up">
                    @php
                        $map = [
                            [
                                'label' => 'Menu',
                                'span' => 'Deve incluir pelo menos 1 item no menu para publicar',
                                'redirect' => '/professional/ementa',
                                'xmark' => 'status1',
                            ],
                            [
                                'label' => 'Permissões',
                                'span' => 'Crie permissões para os seus utilizadores (optional)',
                                'redirect' => '/professional/admin/permissions',
                                'xmark' => 'status2',
                            ],
                            [
                                'label' => 'Utilizadores',
                                'span' => 'Convide utilizadores ao seu restaurante (optional)',
                                'redirect' => '/professional/admin/users',
                                'xmark' => 'status3',
                            ],
                        ];
                    @endphp

                    @foreach ($map as $val)
                        <label style="font-size: 24px; font-weight:600">{{ $val['label'] }}</label>
                        <i class="fa-solid fa-xmark status" id="{{ $val['xmark'] }}"></i><br />
                        <span class="text-muted">{{ $val['span'] }}</span>
                        <button class="btn btn-primary form-control mt-2" onclick="redirect('{{ $val['redirect'] }}')">Ir
                            para
                            {{ $val['label'] }}</button>

                        @if ($val['label'] != 'Utilizadores')
                            <hr class="mt-4">
                        @endif
                    @endforeach
                </div>
            </div>
        </div>        
        <script>
            function redirect(url) {
                window.location.replace(url);
            }

            $("#imageUrl").on('keyup', function() {
                $("#showLogo").attr("src", this.value)
                $("#imgFile").val("");
                $("#hasFile").val(0);
                imgFile = null;
            })

            imgFile = null;
            $('#imgFile').on('change', function() {
                var file = this.files[0];
                console.log(file)
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function() {
                    var base64 = reader.result;
                    imgFile = {
                        "dataURL": base64,
                        "type": file.type
                    };
                    $("#hasFile").val(1);
                    $("#showLogo").attr("src", base64);
                };
            });

            $("#saveGenInfo").on('click', () => {
                map = ["name", "description"]
                invalid = animateErr(map);

                // if (invalid) return;

                if(!$("#imageUrl").val() && $("#hasFile").val()==0) {
                    errorToast("Erro", "Insira um logo");
                    return;
                }

                $.ajax({
                    method: 'post',
                    url: '/professional/saverestaurantinfo',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'name': $("#name").val(),
                        'description': $("#description").val(),
                        'imageUrl': $("#imageUrl").val(),
                        'imageFile': imgFile != null ? imgFile : $("#hasFile").val()
                    },
                }).done((res) => {
                    successToast(res.title, res.message);
                }).fail((err)=>{
                    errorToast(err.responseJSON.title, err.responseJSON.message)
                })
            })

            $(document).ready(() => {
                // get restaurant data
                $.ajax({
                    method: 'post',
                    url: '/professional/getrestaurant',
                    data: {
                        '_token': '{{ csrf_token() }}',
                    }
                }).done((res) => {
                    $("#name").val(res.res_info.name);
                    $("#description").val(res.res_info.description);
                    $("#imageUrl").val(res.res_info.logo_url);
                    console.log(res.res_info);
                    if (res.res_info.logo_url) {
                        $("#showLogo").attr("src", res.res_info.logo_url);
                    } else {
                        $("#hasFile").val(res.res_info.logo_name);
                        $("#showLogo").attr("src", "{{ asset('img/logos') }}" + "/" + res.res_info.logo_name);
                    }
                    if (res.menu_count >= 1) {
                        $("#status1").removeClass('fa-xmark');
                        $("#status1").addClass('fa-check');
                        $("#status1").css('color', 'green');
                    }
                })
            });
        </script>
    @endif
@stop