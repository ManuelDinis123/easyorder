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
        data: Object.assign(form_data, { tags: $("#tags").val(), imageurl: $("#imageurl").val(), "_token": $('#token').val() })
    }).done((res) => {
        if(enter){
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
                "id": 1 // TODO: When you have authentication set up this has to be the current users logged in restaurant
            },
            dataSrc: ''
        },
        columns: [
            { data: "title" },
            { data: "price" },
            {
                data: null,
                render: function (data, type, row, meta) {
                    return '<span>\
                    <a href="/professional/ementa/'+row.id+'"><i class="fa-sharp fa-solid fa-pen" style="color:#1C46B2; cursor:pointer; margin-right:3px;"></i></a>\
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