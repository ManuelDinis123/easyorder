@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'options'])

<link rel="stylesheet" href="{{ asset('css/settings/main.css') }}">

@section('content')

    @include('layouts.professional.tabs', ['tab' => 'general'])

    @if ($info['logo_url'] || $info['logo_name'])
        <style>
            .res-img {
                background: url("{{ $info['logo_name'] ? asset('img/logos/' . $info['logo_name']) : $info['logo_url'] }}");
                background-size: cover !important;
                background-position: center;
            }
        </style>
    @endif
    @if ($info['banner'])
        <style>
            #bannerInput {
                background: url("{!! $info['banner'] !!}");
                background-size: cover !important;
                background-position: center !important;
            }
        </style>
    @endif
    <div class="loaderFADE visually-hidden">
        <div class="loader-container" id="lc">
            <div class="loader2"></div>
        </div>
    </div>
    <div class="container">
        <div class="settings-content">
            <div class="page-card">
                <div class="banner" id="bannerInput"></div>
                <div class="restaurant-logo res-img" id="logoInput"></div>
                <input type="file" class="visually-hidden" id="fileInputLogo" accept="image/*">
                <input type="file" class="visually-hidden" id="fileInputBanner" accept="image/*">
                <input type="text" id="restaurant_name" value="{{ $info['name'] }}"
                    class="form-control mt-1 general-inputs" placeholder="Nome do restaurante">
                <textarea id="desc" class="form-control mt-1 general-inputs textareawidth" rows="5"
                    placeholder="Descrição do restaurante">{{ $info['description'] }}</textarea>
                <button class="btn btn-primary mt-3 mx-2" style="float: right" id="save">Guardar</button>
                <button class="btn btn-danger mt-3" style="float: right" id="cancel">Cancelar</button>
            </div>
        </div>
    </div>

    <script>
        $("#save").on('click', ()=>{
            if(animateErr(["restaurant_name", "desc"])) return;
            $(".loaderFADE").removeClass("visually-hidden");
            $.ajax({
                method: 'post',
                url: '/professional/configuracoes/geral/save',
                data: {
                    "_token": "{{csrf_token()}}",
                    "name": $("#restaurant_name").val(),
                    "description": $("#desc").val(),
                    "logo_url": logoImgFile!=null?logoImgFile:null,
                    "banner": bannerImgFile!=null?bannerImgFile:null,
                }
            }).done((res)=>{
                successToast(res.title, res.message);
                $(".loaderFADE").addClass("visually-hidden");
            }).fail((err)=>{
                errorToast(err.responseJSON.title, err.responseJSON.message)
                $(".loaderFADE").addClass("visually-hidden");
            })
        })
        $("#cancel").on('click', ()=>{
            window.location.reload();
        })

        logoImgFile = null;
        $('#fileInputLogo').on('change', async function() {
            $(".loaderFADE").removeClass("visually-hidden");
            await createBase64(this.files[0])
                .then(imgFile => {
                    logoImgFile = imgFile;
                    $(".res-img").css("background", "url('" + imgFile.dataURL + "')");
                    $(".res-img").css("background-size", "cover");
                    $(".res-img").css("background-position", "center");
                }).catch((err)=>{
                    errorToast("Erro", "ocorreu um erro inesperado!")
                    $(".loaderFADE").addClass("visually-hidden")
                })
            $(".loaderFADE").addClass("visually-hidden");
            console.log(logoImgFile);
        });
        bannerImgFile = null;
        $('#fileInputBanner').on('change', async function() {
            $(".loaderFADE").removeClass("visually-hidden");
            await createBase64(this.files[0])
                .then(imgFile => {
                    bannerImgFile = imgFile;
                    $(".banner").css("background", "url('" + imgFile.dataURL + "')");
                    $(".banner").css("background-size", "cover");
                    $(".banner").css("background-position", "center");
                }).catch((err)=>{
                    errorToast("Erro", "ocorreu um erro inesperado!")
                    $(".loaderFADE").addClass("visually-hidden")
                })
            $(".loaderFADE").addClass("visually-hidden");
            console.log(bannerImgFile);
        });

        $("#logoInput").on('click', () => {
            $("#fileInputLogo").click();
        })
        $("#bannerInput").on('click', () => {
            $("#fileInputBanner").click();
        })

        // Handle the image files
        function createBase64(file) {
            return new Promise((resolve, reject) => {
                var reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = function() {
                    var base64 = reader.result;
                    var imgFile = {
                        "dataURL": base64,
                        "type": file.type
                    };
                    resolve(imgFile);
                };
                reader.onerror = function(error) {
                    reject(error);
                };
            });
        }

        function setLoader() {
            $("body").append(
                `<div class="loader-container" id="lc">
                    <div class="loader2"></div>
                </div>`
            )
        }
    </script>

@stop
