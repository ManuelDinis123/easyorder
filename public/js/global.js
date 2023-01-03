


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
function removeDB(url, idToDelete, hasToast = true) {
    $.ajax({
        method: "post",
        url: url,
        data: {
            "_token": $('#token').val(),
            "id": idToDelete
        }
    }).done((res) => {
        if (!hasToast) return;

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

        if (!hasToast) return;

        iziToast.error({
            title: "Erro",
            message: "Ocorreu um erro a remover o item",
            color: "red",
            icon: "fa-sharp fa-solid fa-triangle-exclamation"
        });
    })
}

/**
 * Make inputs shake when not filled
 * 
 * @requires Array
 */
function animateErr(map, checkForEmpty = true) {
    var hasEmpty = false;
    map.forEach(id => {
        if (!$("#" + id).val() || !checkForEmpty) {
            hasEmpty = true;
            $("#" + id).addClass("animate__animated animate__headShake");
            $("#" + id).addClass("wrong");
            setTimeout(() => {
                $("#" + id).removeClass("animate__animated animate__headShake");
                $("#" + id).removeClass("wrong");
            }, 800);
        }

    });
    return hasEmpty;
}

// TODO: Change all error toasts to this function

/**
 * Toast for error
 */
function errorToast(title, message) {
    iziToast.error({
        title: title,
        message: message,
        color: "red",
        icon: "fa-solid fa-circle-xmark"
    });
}