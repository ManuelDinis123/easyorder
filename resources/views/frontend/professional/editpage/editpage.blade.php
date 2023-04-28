@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'editpage'])

<link rel="stylesheet" href="{{ asset('css/editpage.css') }}">
@section('content')

    @component('components.modal_builder', [
        'modal_id' => 'chooseItemModal',
        'hasHeader' => true,
        'rawHeader' =>
            '<h5 class="modal-title" id="chooseItemModalLabel"><i class="fa-light fa-cards-blank"></i> Escolher Prato do Dia</h5>',
        'hasBody' => true,
        'cards' => $menuItems,
    ])
    @endcomponent

    @component('components.modal_builder', [
        'modal_id' => 'galleryModal',
        'hasHeader' => true,
        'rawHeader' =>
            '<h5 class="modal-title" id="galleryModalLabel"><i class="fa-solid fa-image"></i> Escolher Imagem</h5>',
        'hasBody' => true,
        'inputs' => [
            ['label' => 'Url de Imagem:', 'id' => 'imageURL', 'type' => 'text', 'placeholder' => 'http://image.png'],
            ['label' => '', 'id' => 'card_pos', 'type' => 'hidden'],
        ],
        'hasFooter' => true,
        'buttons' => [
            ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
            ['label' => 'Guardar', 'id' => 'saveUrl', 'class' => 'btn btn-primary'],
        ],
    ])
    @endcomponent

    @component('components.delete', [
        'modal_id' => 'confirmModal',
        'function_name' => 'removePost',
        'hidden' => 'postID',
    ])
        @slot('title')
            Tem a certeza que quer remover esta publicação?
        @endslot
        @slot('span')
            Isto não pode ser revertido.
        @endslot
    @endcomponent

    <style>
        #removePlate {
            @if ($plateofday)
                opacity: 1;
            @else
                opacity: 0;
                pointer-events: none;
            @endif
            transition: all 0.2s;
        }

        #removePlate:hover {
            transform: scale(1.1);
        }

        #removePlate:active {
            transform: scale(0.9);
        }

        .plateofdayimg {
            @if ($plateofday)
                background-image: url("{{ $plateofday['imageUrl'] }}");
                background-size: cover;
                background-position: center;
            @else
                background-color: rgb(241, 241, 241);
            @endif

        }

        .plateofdayimg span {
            @if ($plateofday)
                opacity: 0;
            @else
                opacity: 1;
            @endif
        }
    </style>

    <div class="container">
        <div class="center">
            <h2 style="font-weight: 700">Página Principal</h2><br>
        </div>
        <div class="center mt-4">
            <h4>Prato do Dia<i class="fa-sharp fa-solid fa-x" id="removePlate"
                    style="color: #aa1313; font-size:20px; cursor:pointer;"></i>
            </h4>
        </div>
        <div class="center mt-1 pd-c">
            <div class="plateofday plateofdayimg" id="plateoftheday">
                <span class="pod-msg unselectable text-muted">Nenhum prato selecionado</span>
            </div>
        </div>

        <hr>

        <div class="center mt-4">
            <h2 style="font-weight: 700">Galeria</h2>
        </div>
        <div class="center">
            <span class="text-muted">Estas imagens serão usadas quando o seu restaurante for mostrado na página
                principal</span>
        </div>
        @foreach ($imgs as $i)
            <style>
                #ic{{ $i['card_num'] }} {
                    background: url("{{ $i['imageUrl'] }}");
                    background-size: cover;
                    background-position: center;
                }
            </style>
        @endforeach
        <div class="gallery-container center">
            <div class="row">
                <div class="col-2 image-card" id="ic1" onclick="openGalleryModal(1)"></div>
                <div class="col-2 image-card" id="ic2" onclick="openGalleryModal(2)"></div>
                <div class="col-2 image-card" id="ic3" onclick="openGalleryModal(3)"></div>
                <div class="col-2 image-card" id="ic4" onclick="openGalleryModal(4)"></div>
                <div class="col-2 image-card" id="ic5" onclick="openGalleryModal(5)"></div>
            </div>
        </div>

        <hr>

        <div class="center mt-4">
            <h2 style="font-weight: 700">Publicações</h2><br>
        </div>
        <button class="btn btn-primary" onclick="window.location.href = 'conteudo/publicar'">Criar Nova
            Publicação</button>
        <button class="btn btn-dark" id="changePosts">Ver Rascunhos</button>
        <div class="allPosts mt-4" id="publishedPosts">
            @foreach ($posts as $post)
                <div class="center mt-3">
                    <div class="post" id="post_n{{ $post['id'] }}">
                        <i class="fa-solid fa-pen-to-square post-actions"
                            onclick="window.location.href='/professional/conteudo/publicar?id={{ $post['id'] }}'"
                            style="color: #1C46B2;"></i>
                        <i class="fa-solid fa-trash-can post-actions" onclick="open_delete({{ $post['id'] }})"
                            style="color: rgba(165, 2, 2, 0.719);"></i>
                        <div class="center">
                            <h2>{{ $post['title'] }}</h2>
                        </div>
                        <hr>
                        <input type="hidden" value="0" id="expanded{{ $post['id'] }}">
                        <div class="post-body visually-hidden mt-3" id="body{{ $post['id'] }}">
                            {!! html_entity_decode($post['body']) !!}
                        </div>
                        <button id="expand{{ $post['id'] }}" onclick="expand({{ $post['id'] }})"
                            class="btn btn-dark form-control">Expandir</button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="allPosts visually-hidden mt-4" id="drafts">
            @foreach ($drafts as $draft)
                <div class="center mt-3" id="aDraft{{ $draft['id'] }}">
                    <div class="post">
                        <i class="fa-solid fa-paper-plane post-actions" style="color: #166f17;"
                            onclick="publishDraft({{ $draft['id'] }})"></i>
                        <i class="fa-solid fa-pen-to-square post-actions"
                            onclick="window.location.href='/professional/conteudo/publicar?id={{ $draft['id'] }}'"
                            style="color: #1C46B2;"></i>
                        <i class="fa-solid fa-trash-can post-actions" onclick="open_delete({{ $draft['id'] }})"
                            style="color: rgba(165, 2, 2, 0.719);"></i>
                        <div class="center">
                            <h2 id="post_title{{ $draft['id'] }}">{{ $draft['title'] }}</h2>
                        </div>
                        <hr>
                        <input type="hidden" value="0" id="expanded{{ $draft['id'] }}">
                        <div class="post-body visually-hidden mt-3" id="body{{ $draft['id'] }}">
                            {!! html_entity_decode($draft['body']) !!}
                        </div>
                        <button id="expand{{ $draft['id'] }}" onclick="expand({{ $draft['id'] }})"
                            class="btn btn-dark form-control">Expandir</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // Publish a draft
        function publishDraft(id) {
            $.ajax({
                method: 'post',
                url: '/professional/conteudo/publicar_rascunho',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': id
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#publishedPosts").append(
                    `<div class="center mt-3">\
                    <div class="post">\
                        <i class="fa-solid fa-pen-to-square post-actions"
                            onclick="window.location.href='/professional/conteudo/publicar?id=` + id + `'"
                            style="color: #1C46B2;"></i>\
                        <i class="fa-solid fa-trash-can post-actions" onclick="open_delete(` + id + `)"\
                            style="color: rgba(165, 2, 2, 0.719);"></i>\
                        <div class="center">\
                            <h2>` + $("#post_title" + id).text() + `</h2>\
                        </div>\
                        <hr>\
                        <input type="hidden" value="0" id="expanded` + id + `">\                            
                        <button id="expand` + id + `" onclick="expand(` + id + `)"\
                            class="btn btn-dark form-control">Expandir</button>\
                    </div>\
                </div>`
                );
                $("#body" + id).clone().insertAfter("#expanded" + id);
                $("#aDraft" + id).remove();
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }

        $("#saveUrl").on('click', () => {
            const position = $("#card_pos").val();
            const img = $("#imageURL").val();

            $.ajax({
                method: "post",
                url: "/professional/conteudo/guardar_imagem",
                data: {
                    _token: "{{ csrf_token() }}",
                    pos: position,
                    img: img
                }
            }).done((res) => {
                successToast(res.title, res.message)
                $("#ic" + position).css("background", "url('" + img + "')");
                $("#ic" + position).css("background-size", "cover");
                $("#ic" + position).css("background-position", "center");
                $("#galleryModal").modal("toggle");
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            });
        })

        $('#galleryModal').on('hidden.bs.modal', () => {
            $("#imageURL").val("");
        });

        // opens gallery modal
        function openGalleryModal(pos) {
            $("#card_pos").val(pos);
            $("#galleryModal").modal("toggle");
        }

        // To show the body of a post
        function expand(id) {
            if ($("#expanded" + id).val() == 0) {
                $("#expand" + id).text("Fechar");
                $("#body" + id).removeClass("visually-hidden");
                $("#expanded" + id).val(1);
                return;
            }
            $("#expand" + id).text("Expandir");
            $("#body" + id).addClass("visually-hidden");
            $("#expanded" + id).val(0);
        }

        function open_delete(id) {
            $("#confirmModal").modal("toggle");
            $("#postID").val(id);
        }

        function removePost() {
            $.ajax({
                method: "post",
                url: "/professional/conteudo/delete",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": $("#postID").val()
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#confirmModal").modal("toggle");
                $("#post_n" + $("#postID").val()).remove();
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }

        $("#changePosts").on('click', () => {
            if ($("#drafts").hasClass("visually-hidden")) {
                $("#publishedPosts").addClass("visually-hidden");
                $("#drafts").removeClass("visually-hidden");
                $("#changePosts").text("Ver Publicações");
            } else {
                $("#drafts").addClass("visually-hidden");
                $("#publishedPosts").removeClass("visually-hidden");
                $("#changePosts").text("Ver Rascunhos");
            }
        })

        $("#removePlate").on('click', () => {
            $.ajax({
                method: 'post',
                url: "/professional/conteudo/set",
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $(".plateofdayimg").css("background", "rgb(241, 241, 241)");
                $(".plateofdayimg span").css("opacity", "1");
                $("#removePlate").css("opacity", "0");
                $("#removePlate").css("pointer-events", "none");
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        });

        $("#plateoftheday").on('click', () => {
            $("#chooseItemModal").modal("toggle");
        })

        $(".modal-card-selectable").on('click', function() {
            const itemID = $("#" + this.id + " input").val();
            $.ajax({
                method: "post",
                url: "/professional/conteudo/set",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": itemID
                }
            }).done((res) => {
                successToast(res.title, res.message);
                var imageurl = $(".modal-card" + itemID).css("background");
                imageurl = imageurl.replace(
                    'linear-gradient(rgba(0, 0, 0, 0) 47.4%, rgb(0, 0, 0) 100%) repeat scroll 50% 50% / cover padding-box border-box, rgba(0, 0, 0, 0) url("',
                    "");
                imageurl = imageurl.replace('") repeat scroll 50% 50% / cover padding-box border-box', "");
                $("#chooseItemModal").modal("toggle");
                $(".plateofdayimg").css("background", "url(\"" + imageurl + "\")");
                $(".plateofdayimg").css("background-size", "cover");
                $(".plateofdayimg").css("background-position", "center");
                $(".plateofdayimg span").css("opacity", "0");
                $("#removePlate").css("opacity", "1");
                $("#removePlate").css("pointer-events", "all");
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        })
    </script>

@stop
