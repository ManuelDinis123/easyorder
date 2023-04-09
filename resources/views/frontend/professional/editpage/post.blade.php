<!DOCTYPE html>

@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'editpage'])

<link rel="stylesheet" href="{{ asset('css/editpage.css') }}">
@section('content')


    @component('components.delete', [
        'modal_id' => 'confirmModal',
        'function_name' => 'cancel',
        'dangerLBL' => 'Voltar',
        'hidden' => '',
    ])
        @slot('title')
            Tem a certeza que quer cancelar?
        @endslot
        @slot('span')
            A publicação não ficara guardada.
        @endslot
    @endcomponent


    <style>
        .tox-promotion {
            display: none !important;
        }

        .tox-statusbar__branding {
            display: none !important;
        }

        .tox-statusbar__resize-handle {
            display: none !important;
        }
    </style>

    <div class="loaderFADE visually-hidden">
        <div class="loader-container" id="lc">
            <div class="loader2"></div>
        </div>
    </div>

    <div class="container">
        <div class="center">
            <input type="text" class="form-control title-input" id="post_title" placeholder="Titulo da Publicação"
                value="{{ isset($post) ? $post['title'] : '' }}">
        </div>
        <div class="center mt-3">
            <textarea id="postBody" class="mce-body">{!! isset($post) ? $post['body'] : '' !!}</textarea>
        </div>
        <div class="action-btns mt-3">
            <button class="btn btn-primary" id="publish">Publicar</button>
            <button class="btn btn-secondary" id="draft">Salvar como Rascunho</button>
            <button class="btn btn-danger" id="cancel">Cancelar</button>
        </div>
    </div>

    <script src="{{ mix('plugins/tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <script>
        $("#loading").remove();
        $("#content").removeClass("visually-hidden");

        tinymce.init({
            selector: 'textarea#postBody',
        });

        $("#publish").on('click', () => {
            savePost(1);
        })

        $("#draft").on('click', () => {
            savePost(0);
        })

        $("#cancel").on('click', () => {
            $("#confirmModal").modal("toggle");
        })

        // Function for confirmModal
        function cancel() {
            window.location.href = "/professional/conteudo";
        }

        // Saves the post and gets whether it should be published or not
        function savePost(publish) {
            if (animateErr(["post_title"])) return;
            const text = tinymce.get("postBody").getContent();            
            $.ajax({
                method: "post",
                url: "/professional/conteudo/publicar/save",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "title": $("#post_title").val(),
                    "text": text,
                    "isPublish": publish,
                    "edit": {{ isset($post) ? $post['id'] : 0 }}
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $(".loaderFADE").removeClass("visually-hidden");
                setTimeout(() => {
                    window.location.href = "/professional/conteudo";
                }, 1000);
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }
    </script>

@stop
