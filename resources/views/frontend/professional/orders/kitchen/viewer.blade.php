@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/viewer.css') }}">

<a href="{{ url()->previous() }}"><i class="fa-sharp fa-solid fa-arrow-left back"></i></a>

<div>
    <div class="card mt-5">
        <div class="header">
            <h1>Pedido 1</h1>
            <hr style="width: 200px">
        </div>
        <div class="body">
            <div class="item">
                <span class="title">Hamburguer.</span>
                <span class="sides">Batatas Fritas / Salada</span>
            </div>
            <hr style="width: 50vw">
            <div class="item">
                <span class="title">Galinha Frita.</span>
                <span class="sides">Batatas Fritas</span>
            </div>
            <hr style="width: 50vw">
            <div class="item">
                <span class="title">Bife.</span>
                <span class="sides">Batatas Fritas / Salada / Ovo</span>
            </div>
        </div>
        <div class="footer">
            <h5 class="title" style="float: right">15:30</h5>
        </div>
    </div>
    <div class="card mt-5">
        <div class="header">
            <h1>Pedido 2</h1>
            <hr style="width: 200px">
        </div>
        <div class="body">
            <div class="item">
                <span class="title">Hamburguer.</span>
                <span class="sides">Batatas Fritas / Salada</span>
            </div>
            <hr style="width: 50vw">
            <div class="item">
                <span class="title">Bife.</span>
                <span class="sides">Batatas Fritas / Salada / Ovo</span>
            </div>
        </div>
        <div class="footer">
            <h5 class="title" style="float: right">15:30</h5>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Make the card draggable
        $(".card").draggable({
            axis: "x", // Only allow horizontal dragging
            stop: function(event, ui) {
                // Check if the card has been dragged far enough to the right
                var x = ui.position.left;
                var cardWidth = $(this).outerWidth();
                if (x > cardWidth / 2) {
                    // Animate the card to the right and remove it from the DOM
                    $(this).animate({
                        left: "200%",
                        opacity: 0
                    }, 500, function() {
                        console.log("Pedido pronto supostamente :D")
                        $(this).remove();
                    });
                } else {
                    // Animate the card back to its original position
                    $(this).animate({
                        left: 0
                    }, 200);
                }
            }
        });

        // Make the droppable area the same height as the card
        $(".droppable").height($(".card").outerHeight());

        // Make the droppable area accept the card
        $(".droppable").droppable({
            accept: ".card",
            drop: function(event, ui) {
                // Remove the card from the DOM
                ui.draggable.remove();
            }
        });
    });
</script>
