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
        <hr>
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

        <div class="center mt-5">
            <h2 style="font-weight: 700">Publicações</h2><br>
        </div>
        <hr>
        {{-- Have a scrollable with all posts. Each with a delete and edit. On top have a create post which redirects to another page --}}
        <button class="btn btn-primary">Criar Nova Publicação</button>
    </div>

    <script>
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
