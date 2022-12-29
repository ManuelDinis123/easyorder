@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/menu_edit.css') }}">

<style>
    .img_card {
        margin: 0 auto;
        margin-top: 45px !important;

        background-image: linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%),
            url({{ $imageurl }});
        background-size: cover;
        background-position: center;
        width: 360px;
        height: 360px;

        border-radius: 30px;
    }
</style>


{{-- Delete ingredients modal --}}
@component('components.delete',
    ['modal_id' => 'confirmModal', 'function_name' => 'remove_ing', 'hidden' => 'ingredient_id'])
    @slot('title')
        Quer mesmo apagar este ingrediente?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent

{{-- Edit Ingredients modal --}}
<div class="modal fade" id="editModal" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h3>Editar Ingrediente:</h3>
                <hr>
                <div class="row">
                    <div class="col-11">
                        <label>Nome:</label>
                        <input type="text" class="form-control" id="ingredient_name_edit">
                        <label class="mt-2">Quantidade</label>
                        <input type="number" class="form-control" id="edit_quant">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" onclick="edit_ingredients()">Editar</button>
            </div>
            <input type="hidden" id="id_for_edit">
        </div>
    </div>
</div>


@section('content')

    <div class="row">
        <div class="col-3">
            <span class="btn is-selected" id="geral">Geral</span>
            <span class="btn not-selected" id="ing">Ingredientes</span>
        </div>
        <div class="col-9">
            <i class="fa-solid fa-delete-right del"></i>
        </div>
    </div>
    <hr>
    <div id="general">
        <div class="row">
            <div class="col-5" id="form">
                <label class="mt-1">Nome:</label>
                <input type="text" id="title" class="form-control" placeholder="Nome do item"
                    value="{{ $name }}" autocomplete="off">

                <label class="mt-3">Preço:</label>
                <input type="number" id="price" class="form-control" placeholder="Preço €" value="{{ $price }}"
                    autocomplete="off">

                <label class="mt-3">Custo:</label>
                <input type="number" id="cost" class="form-control" placeholder="Custo de produção €"
                    value="{{ $cost }}" autocomplete="off">

                <label class="mt-3">Imagem:</label>
                <input type="text" id="imageurl" class="form-control" placeholder="https://imageurl.jpg"
                    value="{{ $imageurl }}" autocomplete="off">

                <label class="mt-3">Descrição:</label>
                <textarea type="text" id="description" class="form-control" placeholder="Descrição sobre o item">{{ $description }}</textarea>

                <label class="mt-3">Etiquetas:</label><br />
                <input id="tags" class='customLook' value="{{ $tags }}">
                <button type="button" id="tag_more">+</button>
            </div>

            <div class="col-6">

                <script>
                    imageurlIn = $("#imageurl").val()
                </script>


                <div class="img_card @if (!$imageurl) visually-hidden @endif" id="item-card">
                    <h3 class="img-price h-no-linebreaks" id="card-price">{{ $price }}€</h3>
                    <h3 class="img-h" id="card-name">{{ $name }}</h3>
                </div>

                <label style="text-align: center !important; margin-left: 20%;"
                    class="text-muted @if ($imageurl) visually-hidden @endif" id="card-info">Adicione
                    uma
                    imagem
                    para ter acesso a um cartão para este item</label>

            </div>
            <div class="row mt-3">
                <div class="col-5">
                    <button class="btn btn-primary" id="edit-confirm">Editar</button>
                </div>
            </div>
        </div>
    </div>
    <div id="ingredients" class="visually-hidden">
        <div class="row">
            <div class="col-3">
                <h4>Adicionar Ingredientes</h3>
                    <hr>
                    <label>Nome:</label>
                    <input type="text" class="form-control" placeholder="nome" id="ingredient">

                    <label class="mt-3">Quantidade</label>
                    <input type="number" class="form-control" value="1" min="1" id="quant">
                    <button class="btn btn-primary mt-3" id="add">Adicionar</button>
            </div>
            <div class="col-6">
                <div class="t-contain">
                    <table id="ing_table" class="table table-striped table-borderless">
                        <thead>
                            <th>Ingrediente</th>
                            <th>Quantidade</th>
                            <th></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop

<script>
    // Changes from the current tab to another
    function changeClasses(main, secondary, mainbtn, secondarybtn) {
        if (!$("#" + secondary).hasClass("visually-hidden")) {
            // display
            $("#" + main).removeClass("visually-hidden");
            $("#" + secondary).addClass("visually-hidden");

            $("#" + secondarybtn).removeClass("is-selected");
            $("#" + secondarybtn).addClass("not-selected");
            $("#" + mainbtn).removeClass("not-selected");
            $("#" + mainbtn).addClass("is-selected");

            // animation
            $("#" + main).addClass("fd");
            $("#" + secondary).removeClass("fd");
        }
    }

    // Checks if item name in card has linebreaks or not and changes the position depending on it
    function cardHeaderPosition() {
        // Select the img-h element
        var element = $('.img-h');

        // Get the height of the text within the element
        var textHeight = element[0].scrollHeight;

        // Check if the element has any line breaks
        if (textHeight > 348) {
            $(".img-h").css("padding-top", "290px");
        } else {
            $(".img-h").css("padding-top", "314px");
        }
    }

    // Opens the confirmation modal
    function confirmationModal(id) {
        $("#confirmModal").modal('toggle');

        $("#ingredient_id").val(id);
    }

    // delete ingredients from DB
    function remove_ing() {
        $("#confirmModal").modal('toggle');
        removeDB("/professional/deleteingredient", $("#ingredient_id").val());
        $("#ing_table").DataTable().ajax.reload(null, false);
    }

    // inserts ingredients on the DB
    function add_ing() {
        $.ajax({
            method: "post",
            url: "/professional/addingredients",
            data: {
                "_token": $('#token').val(),
                "ingredient": $('#ingredient').val(),
                "quantity": $('#quant').val(),
                "id": <?= $id ?>,
            }
        }).done((res) => {
            if (res.title == "Sucesso") {
                iziToast.success({
                    title: res.title,
                    message: res.message,
                    color: "green",
                    icon: "fa-solid fa-check"
                });
                $("#ing_table").DataTable().ajax.reload(null, false);
                $("#ingredient").val("");
                $("#quant").val(1);
            } else {
                iziToast.error({
                    title: res.title,
                    message: res.message,
                    color: "red",
                    icon: "fa-sharp fa-solid fa-triangle-exclamation"
                });
            }
        });
    }

    // Opens the edit modal
    function editModal(name, quant, id) {

        $("#ingredient_name_edit").val(name);

        $("#edit_quant").val(quant);

        $("#editModal").modal('toggle');

        $("#id_for_edit").val(id);
    }

    function edit_ingredients() {
        $.ajax({
            method: "post",
            url: "/professional/updateingredients",
            data: {
                "_token": $('#token').val(),
                "id": <?= $id ?>,
                "ingid": $("#id_for_edit").val(),
                "ingredient": $("#ingredient_name_edit").val(),
                "quantity": $("#edit_quant").val(),
            }
        }).done((res) => {
            if (res.title == "Sucesso") {
                iziToast.success({
                    title: res.title,
                    message: res.message,
                    color: "green",
                    icon: "fa-solid fa-check"
                });
                $("#ing_table").DataTable().ajax.reload(null, false);
            } else {
                iziToast.error({
                    title: res.title,
                    message: res.message,
                    color: "red",
                    icon: "fa-sharp fa-solid fa-triangle-exclamation"
                });
            }
        })
    }

    // Update the data on the item card
    function updateCard() {
        $("#card-name").text($("#title").val());
        $("#card-price").text($("#price").val() + "€");


        $(".img_card").css("background-image", "linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%), url(\"" +
            $("#imageurl").val() + "\")");


        if ($("#imageurl").val() === "") {
            $("#item-card").addClass("visually-hidden");
            $("#card-info").removeClass("visually-hidden");
        } else {
            $("#item-card").removeClass("visually-hidden");
            $("#card-info").addClass("visually-hidden");
        }
    }

    $(document).ready(() => {

        // Clicking enter submits the ingredient data.
        $("#ingredients").on('keypress', (e) => {
            if (e.which == 13)
                add_ing();
        })


        $("#add").on('click', () => {
            add_ing();
        });

        $("#form").on('keyup', () => {
            updateCard();
            cardHeaderPosition();
        })

        $("#geral").on('click', () => {
            changeClasses("general", "ingredients", "geral", "ing");
        });
        $("#ing").on('click', () => {
            changeClasses("ingredients", "general", "ing", "geral");
        });

        $("#edit-confirm").on('click', () => {
            $.ajax({
                method: "post",
                url: "/professional/updatemenuitem",
                data: {
                    "_token": $('#token').val(),
                    "id": <?= $id ?>,
                    "name": $("#title").val(),
                    "price": $("#price").val(),
                    "cost": $("#cost").val(),
                    "imageurl": $("#imageurl").val(),
                    "description": $("#description").val(),
                    "tags": $("#tags").val(),
                }
            }).done((res) => {
                if (res.title == "Sucesso") {
                    iziToast.success({
                        title: res.title,
                        message: res.message,
                        color: "green",
                        icon: "fa-solid fa-check"
                    });
                } else {
                    iziToast.error({
                        title: res.title,
                        message: res.message,
                        color: "red",
                        icon: "fa-sharp fa-solid fa-triangle-exclamation"
                    });
                }
            })
        })

        // Initialize the datatable
        $("#ing_table").dataTable({

            "ordering": false,

            "language": {
                "paginate": {
                    "next": '<i class="fa-solid fa-caret-right"></i>',
                    "previous": '<i class="fa-solid fa-caret-left"></i>'
                }
            },

            ajax: {
                method: "post",
                url: '/professional/getingredients',
                data: {
                    "_token": $('#token').val(),
                    "id": <?= $id ?>
                },
                dataSrc: ''
            },
            columns: [{
                    data: "ingredient",
                    width: "20%"
                },
                {
                    data: "quantity",
                    width: "10%",
                    className: "dt-center"
                },
                {
                    data: null,
                    width: "30%",
                    render: function(data, type, row, meta) {
                        return '<div  style="display: flex; align-items: center">\
                                <i onClick="editModal(\'' + row.ingredient + '\', ' + row.quantity + ', ' + row.id + ')" class="fa-sharp fa-solid fa-pen" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i>\
                                <i onClick="confirmationModal(' + row.id + ')" class="fa-sharp fa-solid fa-trash-xmark" style="color:#bf1313; cursor:pointer;"></i>\
                                </div>';
                    }
                },
            ]
        });


        // Tagify
        let tags_suggestions = [];

        // Get the tags from the db
        $.ajax({
            method: 'post',
            url: '/professional/gettags',
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

        cardHeaderPosition();

    });
</script>
