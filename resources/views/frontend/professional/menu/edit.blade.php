@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'menu'])

<link rel="stylesheet" href="{{ asset('css/menu_edit.css') }}">

<style>
    .img_card {
        display: flex;
        align-items: flex-end;
        margin: 0 auto;
        margin-top: 45px !important;
        padding: 5px 5px;
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
@component('components.delete', [
    'modal_id' => 'confirmModal',
    'function_name' => 'remove_ing',
    'hidden' => 'ingredient_id',
])
    @slot('title')
        Quer mesmo apagar este ingrediente?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent


{{-- Delete item modal --}}
@component('components.delete', [
    'modal_id' => 'itemDelModal',
    'function_name' => 'remove_itm',
    'hidden' => 'item_id',
])
    @slot('title')
        Tem a certeza que quer remover este item?
    @endslot
    @slot('span')
        Isto não pode ser revertido
    @endslot
@endcomponent

{{-- Edit Ingredients modal --}}
@component('components.modal_builder', [
    'modal_id' => 'editModal',
    'hasHeader' => true,
    'modalTitle' => 'Editar Acompanhamento:',
    'hasBody' => true,
    'inputs' => [
        ['label' => 'Nome:', 'id' => 'ingredient_name_edit', 'type' => 'text'],
        ['label' => 'Quantidade:', 'id' => 'edit_quant', 'type' => 'number'],
        ['label' => 'Preço:', 'id' => 'edit_price', 'type' => 'number'],
        ['label' => '', 'id' => 'id_for_edit', 'type' => 'hidden'],
    ],
    'select' => [
        'configs' => [
            'id' => 'edit_typeQ',
            'label' => 'Tipo de Quantidade:',
            'default' => 'Selecione um tipo',
        ],
        'options' => [['value' => 'numeric', 'label' => 'Numerico'], ['value' => 'dose', 'label' => 'Dose']],
    ],
    'rawBody' => '<div class="form-check form-switch mt-3">
        <label class="form-check-label" for="edit_default">Escolhido por Padrão</label>
        <input class="form-check-input" type="checkbox" id="edit_default">
        </div>',
    'hasFooter' => true,
    'buttons' => [
        ['label' => 'Cancelar', 'id' => 'closeMdl', 'class' => 'btn btn-danger', 'dismiss' => true],
        [
            'label' => 'Editar',
            'id' => 'edtIngredientsBtn',
            'class' => 'btn btn-primary',
            'function' => 'edit_ingredients',
        ],
    ],
])
@endcomponent

@php
    $canWrite = session()->get('type.write_menu') || session()->get('type.owner') || session()->get('type.admin');
    $disable = session()->get('type.write_menu') || session()->get('type.owner') || session()->get('type.admin') ? '' : 'disabled';
@endphp

@section('content')
    {{-- Breadcrumbs --}}
    @component('components.breadcrumbs', [
        'title' => $name,
        'crumbs' => [
            ['link' => '/professional/ementa', 'label' => 'Menu'],
            ['link' => '/professional/ementa/' . $id, 'label' => $name],
        ],
    ])
    @endcomponent

    <div class="row">
        <div class="col-3">
            <span class="btn is-selected" id="geral">Geral</span>
            <span class="btn not-selected" id="ing">Acompanhamentos</span>
        </div>
        @if ($canWrite)
            <div class="col-9">
                <i class="fa-solid fa-delete-right del" data-bs-toggle="modal" data-bs-target="#itemDelModal"></i>
            </div>
        @endif
    </div>
    <hr>
    <div id="general">
        <div class="row">
            <div class="col-lg-5 col-md-5 col-sm-12" id="form">
                <label class="mt-1">Nome:</label>
                <input type="text" id="item_title" {{ $disable }} class="form-control" placeholder="Nome do item"
                    value="{{ $name }}" autocomplete="off">

                <label class="mt-3">Preço:</label>
                <input type="number" id="price" {{ $disable }} class="form-control" placeholder="Preço €"
                    value="{{ $price + 0 }}" autocomplete="off">

                <label class="mt-3">Custo:</label>
                <input type="number" id="cost" {{ $disable }} class="form-control"
                    placeholder="Custo de produção €" value="{{ $cost + 0 }}" autocomplete="off">

                <label class="mt-3">Imagem:</label>
                <input type="file" id="imageurl" {{ $disable }} class="form-control"
                    placeholder="https://imageurl.jpg" value="{{ $imageurl }}" autocomplete="off" accept="image/*">

                <label class="mt-3">Descrição:</label>
                <textarea type="text" id="description" {{ $disable }} class="form-control"
                    placeholder="Descrição sobre o item">{{ $description }}</textarea>

                <label class="mt-3">Etiquetas:</label><br />
                <input id="db_tags" class='tags_db' value="{{ $tags }}" {{ $disable }}>
                @if ($canWrite)
                    <input id="tags" class='customLook'>
                    <button type="button" id="tag_more">+</button>
                @endif
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12">

                <script>
                    imageurlIn = $("#imageurl").val()
                </script>


                <div class="img_card @if (!$imageurl) visually-hidden @endif" id="item-card">
                    <h3 class="img-h" id="card-name" style="width: 289px">{{ $name }}</h3>
                    <h3 class="img-price h-no-linebreaks" id="card-price">{{ $price + 0 }}€</h3>
                </div>

                <label style="text-align: center !important; margin-left: 20%;"
                    class="text-muted @if ($imageurl) visually-hidden @endif" id="card-info">Adicione
                    uma
                    imagem
                    para ter acesso a um cartão para este item</label>

            </div>
            <div class="row mt-3">
                <div class="col-lg-5 col-md-5 col-sm-12">
                    @if ($canWrite)
                        <button class="btn btn-primary" id="edit-confirm" style="width: 100%">Confirmar</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div id="ingredients" class="visually-hidden">
        <div class="row">
            @if ($canWrite)
                <div class="col-lg-3 col-md-6 col-sm-12">
                    <h4>Adicionar Acompanhamentos</h3>
                        <hr>
                        <label>Nome:</label>
                        <input type="text" class="form-control" placeholder="nome" id="ingredient">

                        <label class="mt-3" style="padding-right: 10px">Quantidade</label><br />
                        <span class="text-muted">Apenas funcional se escolhido por padrão</span>
                        <input type="number" class="form-control" value="1" min="1" id="quant">

                        <label class="mt-3">Preço</label>
                        <input type="number" class="form-control" value="0" min="1" id="ing_price">

                        <label class="mt-3">Tipo de Quantidade</label>
                        <select id="quantType" class="form-select">
                            <option disabled selected>Selecione um Tipo</option>
                            <option value="numeric">Numerico</option>
                            <option value="dose">Dose</option>
                        </select>

                        <div class="form-check form-switch mt-3">
                            <label class="form-check-label" for="choosenDFT">Escolhido por padrão</label>
                            <input class="form-check-input" type="checkbox" id="choosenDFT">
                        </div>

                        <button class="btn btn-primary mt-3" id="add">Adicionar</button>
                </div>
            @endif
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="t-contain">
                    <table id="ing_table" class="table table-striped table-borderless" style="width: 100%">
                        <thead>
                            <th>Acompanhamento</th>
                            <th>Quantidade</th>
                            <th>Preço</th>
                            <th>Tipo de Quantidade</th>
                            <th>Escolhido por Padrão</th>
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
            $("#" + secondarybtn).removeClass("animate__animated animate__pulse");
            $("#" + mainbtn).addClass("animate__animated animate__pulse");

            // animation
            $("#" + main).addClass("animate__animated animate__fadeIn");
            $("#" + secondary).removeClass("animate__animated animate__fadeIn");
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

    // Delete item from DB
    function remove_itm() {
        var dl = removeDB("/professional/deletemenuitem", <?= $id ?>);
        $("#itemDelModal").modal("toggle");
        window.location.replace("/professional/ementa");
    }

    // inserts ingredients on the DB
    function add_ing() {
        var map = ["ingredient", "quant", "quantType"];
        var invalid = animateErr(map);

        if (!invalid) {
            $.ajax({
                method: "post",
                url: "/professional/addingredients",
                data: {
                    "_token": $('#token').val(),
                    "ingredient": $('#ingredient').val(),
                    "quantity": $('#quant').val(),
                    "price": $('#ing_price').val(),
                    "quantityType": $('#quantType').val(),
                    "default": $('#choosenDFT').is(':checked') ? '1' : '0',
                    "id": <?= $id ?>,
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#ing_table").DataTable().ajax.reload(null, false);
                $("#ingredient").val("");
                $("#quant").val(1);
                $("#quantType").val(0);
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            });
        }
    }

    // Opens the edit modal
    function editModal(name, quant, quantType, id, isDefault, price) {

        $("#ingredient_name_edit").val(name);

        $("#edit_quant").val(quant);

        $("#edit_typeQ").val(quantType);

        $("#edit_price").val(price);

        $("#editModal").modal('toggle');

        $("#edit_default").prop('checked', isDefault ? true : false);

        $("#id_for_edit").val(id);
    }

    function edit_ingredients() {
        var map = ["ingredient_name_edit", "edit_quant", "edit_typeQ"];
        var invalid = animateErr(map);

        if (!invalid) {
            $.ajax({
                method: "post",
                url: "/professional/updateingredients",
                data: {
                    "_token": $('#token').val(),
                    "id": <?= $id ?>,
                    "ingid": $("#id_for_edit").val(),
                    "ingredient": $("#ingredient_name_edit").val(),
                    "quantity": $("#edit_quant").val(),
                    "price": $("#edit_price").val(),
                    "quantityType": $("#edit_typeQ").val(),
                    "default": $("#edit_default").is(":checked") ? 1 : 0,
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#ing_table").DataTable().ajax.reload(null, false);
                $("#editModal").modal("toggle");
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }
    }

    // Update the data on the item card
    function updateCard() {
        $("#card-name").text($("#item_title").val());
        $("#card-price").text($("#price").val() + "€");


        $(".img_card").css("background-image", "linear-gradient(180deg, rgba(0, 0, 0, 0) 47.4%, #000000 100%), url(\"" +
            imgFile.dataURL + "\")");

        if (imgFile === null) {
            $("#item-card").addClass("visually-hidden");
            $("#card-info").removeClass("visually-hidden");
            $("#item-card").removeClass("animate__animated animate__bounceIn");
        } else {
            $("#card-info").addClass("visually-hidden");
            $("#item-card").removeClass("visually-hidden");
            $("#item-card").addClass("animate__animated animate__bounceIn");
        }
    }

    $(document).ready(() => {

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
                updateCard();
            };
        });

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
            var map = ["item_title", "price", "cost", "description"];
            invalid = animateErr(map);

            if (!invalid) {
                $.ajax({
                    method: "post",
                    url: "/professional/updatemenuitem",
                    data: {
                        "_token": $('#token').val(),
                        "id": <?= $id ?>,
                        "name": $("#item_title").val(),
                        "price": $("#price").val(),
                        "cost": $("#cost").val(),
                        "imageurl": imgFile != null ? imgFile.dataURL : "",
                        "description": $("#description").val(),
                        "tags_in_db": $("#db_tags").val(),
                        "tags": $("#tags").val(),
                    }
                }).done((res) => {
                    if (res.title == "Sucesso") {
                        $("#breadcrumb_title").text($("#item_title").val());
                        successToast(res.title, res.message);
                    }
                }).fail((err) => {
                    errorToast(err.responseJSON.title, err.responseJSON.message);
                })
            }
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
                    data: "price",
                    width: "10%",
                    className: "dt-center",
                    render: function(data, type, row, meta) {
                        return "<label>" + data + "€</label>"
                    }
                },
                {
                    data: "quantity_type",
                    width: "20%",
                    className: "dt-center",
                    render: function(data, type, row, meta) {
                        return "<label>" + (data == 'numeric' ? 'Numerico' : 'Dose') +
                            "</label>"
                    }
                },
                {
                    data: "default",
                    width: "10%",
                    className: "dt-center",
                    render: function(data, type, row, meta) {
                        return "<div class=\"form-check form-switch\">\
                                <input class=\"form-check-input\" type=\"checkbox\" disabled " + (data ? 'checked' :
                            '') + ">\
                                </div>"
                    }
                },
                {
                    data: null,
                    width: "30%",
                    render: function(data, type, row, meta) {
                        if (!{{ $canWrite ? 1 : 0 }}) return "";

                        return '<div  style="display: flex; align-items: center">\
                                <i onClick="editModal(\'' + row.ingredient + '\', ' + row.quantity + ', \'' + row
                            .quantity_type + '\', ' + row.id + ', ' + row.default+', ' + row
                            .price + ')" class="fa-sharp fa-solid fa-pen" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i>\
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
            var input = document.querySelector('.tags_db'),
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
