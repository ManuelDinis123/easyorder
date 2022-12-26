$(document).ready(() => {
    $("#header-toggle").on('click', () => {
        $("#nav-bar").toggleClass("show_side");
        $("#header-toggle").toggleClass("fa-xmark");
        $("#body-pd").toggleClass("body-pd");
        $("#header").toggleClass("body-pd");
    });
});