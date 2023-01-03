@include('layouts.includes')
<!-- Create New Modal -->
<div class="modal fade" id="addModal" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel"><i class="fa-solid fa-circle-plus text-icon"></i> Adicionar Novo
                    Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="mt-1">Nome:</label>
                <input type="text" id="title" class="form-control" placeholder="Nome do item">

                <label class="mt-3">Preço:</label>
                <input type="number" id="price" class="form-control" placeholder="Preço €">

                <label class="mt-3">Custo:</label>
                <input type="number" id="cost" class="form-control" placeholder="Custo de produção €">

                <label class="mt-3">Descrição:</label>
                <textarea type="text" id="description" class="form-control" placeholder="Descrição sobre o item"></textarea>

                <label class="mt-1">Imagem:</label><br />
                <span class="text-muted" style="font-size: 15px">(optional)</span>
                <input type="text" id="imageurl" class="form-control" placeholder="https://imageurl.jpg">

                <label class="mt-3">Etiquetas:</label><br />
                <span class="text-muted" style="font-size: 15px">(optional)</span><br />
                <input id="tags" class='customLook'>
                <button type="button" id="tag_more">+</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="save">Guardar</button>
                <button type="button" class="btn btn-primary" id="save_enter">Guardar e abrir</button>
            </div>
        </div>
    </div>
</div>

@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/menu.css') }}">

{{-- Confirm delete modal --}}
@component('components.delete', ['modal_id' => 'confirmModal', 'function_name' => 'remove', 'hidden' => 'item_id'])
    @slot('title')
        Tem a certeza que quer remover este item?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent

@section('content')
    <div class="container-fluid" style="padding-top:15px">
        <div class="centered">
            <div class="c-contents">

                {{-- Header --}}
                <div class="row">
                    <div class="col-6">
                        <h3 class="c-h">Ementa</h3>
                    </div>
                    <div class="col-6">
                        <span class="icons" data-bs-toggle="modal" data-bs-target="#addModal"><i
                                class="fa-solid fa-plus"></i></span>
                    </div>
                </div>

                <hr style="height: 2px;" class="separation-line">

                {{-- Table --}}
                <table id="menu" class="table table-borderless">
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
                imageurl: $("#imageurl").val(),
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
                title: "Erro",
                message: "Ocorreu um erro ao guardar",
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
                    data: "title"
                },
                {
                    data: "price"
                },
                {
                    data: null,
                    render: function(data, type, row, meta) {
                        return '<span>\
                    <a href="/professional/ementa/' + row.id + '"><i class="fa-sharp fa-solid fa-pen" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i></a>\
                    <i onClick="confirmationModal(' + row.id + ')" class="fa-sharp fa-solid fa-trash-xmark" style="color:#bf1313; cursor:pointer;"></i>\
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
