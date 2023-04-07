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
            <h2 style="font-weight: 700">Publicações</h2><br>
        </div>
        <button class="btn btn-primary" onclick="window.location.href = 'conteudo/publicar'">Criar Nova
            Publicação</button>
        <button class="btn btn-dark" id="changePosts">Ver Rascunhos</button>
        <div class="allPosts mt-4" id="publishedPosts">
            @foreach ($posts as $post)
                <div class="center mt-3">
                    <div class="post">
                        <i class="fa-solid fa-pen-to-square post-actions" style="color: #1C46B2;"></i>
                        <i class="fa-solid fa-trash-can post-actions" style="color: rgba(165, 2, 2, 0.719);"></i>
                        <div class="center">
                            <h2>{{ $post['title'] }}</h2>
                        </div>
                        <hr>                        
                        <input type="hidden" value="0" id="expanded{{$post['id']}}">
                        <div class="post-body visually-hidden mt-3" id="body{{ $post['id'] }}">
                            {!! html_entity_decode($post['body']) !!}
                        </div>
                        <button id="expand{{ $post['id'] }}" onclick="expand({{ $post['id'] }})" class="btn btn-dark form-control">Expandir</button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="allPosts mt-4 visually-hidden" id="drafts">
            @foreach ($drafts as $draft)
                <div class="center mt-3">
                    <div class="post">
                        <i class="fa-solid fa-pen-to-square post-actions" style="color: #1C46B2;"></i>
                        <i class="fa-solid fa-trash-can post-actions" style="color: rgba(165, 2, 2, 0.719);"></i>
                        <div class="center">
                            <h2>{{ $draft['title'] }}</h2>
                        </div>
                        <hr>
                        <input type="hidden" value="0" id="expanded{{$draft['id']}}">
                        <div class="post-body visually-hidden mt-3" id="body{{ $draft['id'] }}">
                            {!! html_entity_decode($draft['body']) !!}
                        </div>
                        <button id="expand{{ $draft['id'] }}" onclick="expand({{ $draft['id'] }})" class="btn btn-dark form-control">Expandir</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        // To show the body of a post
        function expand(id) {
            if($("#expanded"+id).val()==0){
                $("#expand"+id).text("Fechar");
                $("#body"+id).removeClass("visually-hidden");
                $("#expanded"+id).val(1);
                return;
            }
            $("#expand"+id).text("Expandir");
            $("#body"+id).addClass("visually-hidden");
            $("#expanded"+id).val(0);
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
