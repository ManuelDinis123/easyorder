// Performs the login
function login_action() {
    $.ajax({
        method: "post",
        url: "auth",
        data: {
            "_token": $('#token').val(),
            email: $("#email").val(),
            password: $("#password").val()
        }
    }).done((res) => {        
        if (res.status == "success") {
            window.location.replace(res.isProfessional ? "/professional" : "/home");
        } else {
            iziToast.error({
                title: res.status,
                message: res.message,
                color: "red",
                icon: "fa-solid fa-circle-xmark"
            });
        }
    });
}

$(document).ready(() => {
    $("#login_btn").on('click', () => {
        login_action();
    })
    $(document).on('keypress', (e) => {
        if (e.which == 13)
            login_action();
    })
});