
/**
 * Check if values in Object are empty or not
 * 
 * @requires Object
 * @returns Boolean
 */
function objectIsEmpty(values) {
    for (const i of Object.keys(values)) {
        if (values[i] == '' || values[i] == null || values[i] == NaN || values[i] == undefined) {
            return true;
        }
    }
    return false;
}

/**
 * Does all the logic to remove something from DB with ajax
 * 
 */
function removeDB(url, idToDelete) {
    $.ajax({
        method: "post",
        url: url,
        data: {
            "_token": $('#token').val(),
            "id": idToDelete
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
    }).fail((err) => {
        iziToast.error({
            title: "Erro",
            message: "Ocorreu um erro a remover o item",
            color: "red",
            icon: "fa-sharp fa-solid fa-triangle-exclamation"
        });
    })
}