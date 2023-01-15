$(document).ready(() => {
    $("#header-toggle").on('click', () => {
        $("#nav-bar").toggleClass("show_side");
        $("#header-toggle").toggleClass("fa-xmark");
        $("#body-pd").toggleClass("body-pd");
        $("#header").toggleClass("body-pd");
    });

    $(".leave").on('click', () => {
        $.ajax({
            method: 'post',
            url: '/logout',
            data: {
                "_token": $('#token').val(),
            }
        }).done(res=>{
            window.location.replace(res);
        })
    })
});