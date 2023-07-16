@include('layouts.includes')
<!-- Create New Modal -->
@component('components.modal_builder', [
    'modal_id' => 'addModal',
    'hasHeader' => true,
    'rawHeader' =>
        '<h5 class="modal-title" id="addModalLabel"><i class="fa-solid fa-circle-plus text-icon"></i> Adicionar Novo Item</h5>',
    'hasBody' => true,
    'inputs' => [
        ['label' => 'Nome:', 'type' => 'text', 'id' => 'title', 'placeholder' => 'Nome do item'],
        ['label' => 'Preço:', 'type' => 'number', 'id' => 'price', 'placeholder' => 'Preço €'],
        ['label' => 'Custo:', 'type' => 'number', 'id' => 'cost', 'placeholder' => 'Custo de produção €'],
        [
            'label' => 'Descrição:',
            'type' => 'text',
            'id' => 'description',
            'placeholder' => 'Descrição sobre o item',
            'isTextarea' => true,
        ],
        [
            'label' => 'Imagem:',
            'type' => 'file',
            'id' => 'imageurl',
            'placeholder' => 'https://imageurl.jpg',
            'optional' => true,
            'restrictFile' => true,
        ],
    ],
    'rawBody' => '<label class="mt-3">Etiquetas:</label><br />
                <span class="text-muted" style="font-size: 15px">(optional)</span><br />
                <input id="tags" class="customLook">
                <button type="button" id="tag_more">+</button>',
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
        ['label' => 'Guardar', 'id' => 'save', 'class' => 'btn btn-success'],
        ['label' => 'Guardar e abrir', 'id' => 'save_enter', 'class' => 'btn btn-primary'],
    ],
])
@endcomponent

@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/menu.css') }}">

{{-- Confirm delete modal --}}
@component('components.delete', [
    'modal_id' => 'confirmModal',
    'function_name' => 'remove',
    'hidden' => 'item_id',
])
    @slot('title')
        Tem a certeza que quer remover este item?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent

@php
    $canWrite = session()->get('type.write_menu') || session()->get('type.owner') || session()->get('type.admin');
@endphp
<title>Ementa</title>
@section('content')
    <div class="center">
        <div class="centered">
            <div class="c-contents">
                {{-- Header --}}
                <div class="row">
                    <div class="col-6">
                        <h3 class="c-h">Ementa</h3>
                    </div>
                    <div class="col-6">
                        @if ($canWrite)
                            <span class="icons" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                    class="fa-solid fa-plus"></i></span>
                        @endif
                    </div>
                </div>

                <hr style="height: 2px;" class="separation-line">

                {{-- Table --}}
                <table id="menu" class="table table-borderless" style="width: 100%">
                    <thead>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@stop

<script>
    // Clears modal
    function clearModal() {
        $("#title").val("");
        $("#price").val("");
        $("#description").val("");
        $("#imageurl").val("");
        $("#cost").val("");
        $("#tags").val("");
    }

    imgFile = null;
    $('#imageurl').on('change', function() {
        var file = this.files[0];
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function() {
            var base64 = reader.result;
            imgFile = {
                "dataURL": base64,
                "type": file.type
            };
        };
    });

    // saves the item data to Database
    function saveData(enter = false) {
        map = ["title", "price", "cost", "description"];
        animateErr(map);


        // The required data from the form
        var form_data = {
            name: $("#title").val(),
            price: $("#price").val(),
            cost: $("#cost").val(),
            description: $("#description").val(),
        }

        // If object has any empty value show an Error
        if (objectIsEmpty(form_data)) {
            iziToast.error({
                title: "Erro",
                message: "Preencha todos os campos",
                color: "red",
                icon: "fa-sharp fa-solid fa-triangle-exclamation"
            });
            return;
        }

        $.ajax({
            method: "post",
            url: "createmenuitem",
            data: Object.assign(form_data, {
                tags: $("#tags").val(),
                imageurl: imgFile != null ? imgFile.dataURL : "",
                "_token": $('#token').val()
            })
        }).done((res) => {
            if (enter) {
                window.location.replace("/professional/ementa/" + res.id);
                return;
            }

            iziToast.success({
                title: res.title,
                message: res.message,
                color: "green",
                icon: "fa-solid fa-check"
            });
            clearModal();
            $("#addModal").modal('toggle');
            $("#menu").DataTable().ajax.reload(null, false);
        }).fail((err) => {
            iziToast.error({
                title: err.responseJSON.title,
                message: err.responseJSON.message,
                color: "red",
                icon: "fa-sharp fa-solid fa-triangle-exclamation"
            });
        })
    }

    // delete item from DB
    function remove() {
        $("#confirmModal").modal('toggle');
        removeDB("deletemenuitem", $("#item_id").val());
        $("#menu").DataTable().ajax.reload(null, false);
    }

    // Opens the confirmation modal
    function confirmationModal(id) {
        $("#confirmModal").modal('toggle');

        $("#item_id").val(id);
    }

    $(document).ready(() => {

        var restaurant = {!! json_encode(session()->get('restaurant')) !!};

        $("#addModal").on('hide.bs.modal', function() {
            clearModal();
        });

        $("#save").on('click', () => {
            saveData();
        });

        $("#save_enter").on('click', () => {
            saveData(true);
        })

        // Initialize the datatable
        $("#menu").dataTable({

            "ordering": false,

            "language": {
                "paginate": {
                    "next": '<i class="fa-solid fa-caret-right"></i>',
                    "previous": '<i class="fa-solid fa-caret-left"></i>'
                }
            },

            ajax: {
                method: "post",
                url: 'getmenu',
                data: {
                    "_token": $('#token').val(),
                    "id": restaurant.id
                },
                dataSrc: ''
            },
            columns: [{
                    data: "title",
                    width: "65%"
                },
                {
                    data: "price",
                    width: "20%"
                },
                {
                    data: null,
                    width: "15%",
                    render: function(data, type, row, meta) {
                        return '<span>\
                    <a href="/professional/ementa/' + row.id + '"><i class="fa-sharp fa-solid ' + (
                            {{ $canWrite ? 1 : 0 }} ? 'fa-pen' : 'fa-eye') + '" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i></a>\
                    <i onClick="confirmationModal(' + row.id + ')" class="fa-sharp fa-solid fa-trash-xmark' + (
                            {{ $canWrite ? 1 : 0 }} ? '' : 'visually-hidden') + '" style="color:#bf1313; cursor:pointer;"></i>\
                    </span>';
                    }
                },
            ]
        });

        let tags_suggestions = [];

        // Get the tags from the db
        $.ajax({
            method: 'post',
            url: 'gettags',
            data: {
                "_token": $('#token').val()
            }
        }).done((tags) => {
            $.each(tags, (key, tag) => {
                tags_suggestions.push(tag.tag);
            })
            // Initialize tagify
            var input = document.querySelector('.customLook'),
                tagify = new Tagify(input, {
                    whitelist: tags_suggestions,
                    pattern: /^[^ -]*$/,
                    callbacks: {
                        "invalid": onInvalidTag
                    },
                    dropdown: {
                        position: 'text',
                        enabled: 1 // show suggestions dropdown after 1 typed character
                    }
                });
            $("#tag_more").on("click", onAddButtonClick)

            function onAddButtonClick() {
                tagify.addEmptyTag()
            }

            function onInvalidTag(e) {
                iziToast.error({
                    title: "Erro",
                    message: "Etiquetas não devem ter espaços ou -",
                    color: "red",
                    icon: "fa-sharp fa-solid fa-triangle-exclamation"
                });
            }
        });


    });
</script>
