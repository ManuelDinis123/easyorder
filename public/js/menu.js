// TODO: Action Functionality


// TODO: these are placeholder values, they have to be from the database
let tags_suggestions = ["Carne", "FastFood", "Vegetais"];

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

$(document).ready(() => {

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
            { data: "actions" },
        ]
    });

    $("#save").on('click', () => {
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
            data: Object.assign(form_data, {tags: $("#tags").val(), "_token": $('#token').val()})
        }).done((res) => {
            iziToast.success({
                title: res.title,
                message: res.message,
                color: "green",
                icon: "fa-solid fa-check"
            });
        }).fail((err)=>{
            iziToast.error({
                title: "Erro",
                message: "Ocorreu um erro ao guardar",
                color: "red",
                icon: "fa-sharp fa-solid fa-triangle-exclamation"
            });
        })
    });

});