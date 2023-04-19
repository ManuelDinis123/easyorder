// Performs the login
function login_action() {
    $.ajax({
        method: "post",
        url: "auth",
        data: {
            _token: $("#token").val(),
            email: $("#email").val(),
            password: $("#password").val(),
        },
    })
        .done((res) => {
            window.location.replace(
                res.isProfessional ? "/professional" : "/home"
            );
        })
        .fail((err) => {
            errorToast(err.responseJSON.status, err.responseJSON.message);
        });
}

$(document).ready(() => {
    $("#login_btn").on("click", () => {
        login_action();
    });
    $(document).on("keypress", (e) => {
        if (e.which == 13) login_action();
    });
});
