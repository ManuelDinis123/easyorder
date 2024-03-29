@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/viewer.css') }}">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>

<a href="{{ url()->previous() }}"><i class="fa-sharp fa-solid fa-arrow-left back"></i></a>

<div class="loader-container" id="lc">
    <div class="loader2"></div>
</div>

<div id="orders"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.6.1/socket.io.js"></script>
<script>
    $(document).ready(() => {
        const socket = io('http://localhost:3000');
        socket.on('connect', () => {
            socket.emit('restaurant', {{ session()->get('restaurant.id') }});
        })


        let ordersData = {};
        let time;
        socket.on('orders', (data) => {
            $("#lc").remove();
            if (Object.keys(ordersData).length != Object.keys(data).length) {
                if (Object.keys(data).length == 0) {
                    $("#orders div").remove();
                } else {
                    $.each(data, (key, orders) => {
                        if (ordersData[key] && data[key]) {
                            if (Object.keys(ordersData).length > Object.keys(data).length) {
                                var removed = Object.keys(ordersData).filter(x => Object.keys(
                                        data)
                                    .includes(x));

                                if (Object.keys(data).length != removed.length) {
                                    $.each(removed, (k, v) => {
                                        $("#order_" + v).remove();
                                    })
                                }

                            }
                        } else {
                            $("#orders").append(
                                "<div class='card mt-5 bounce' id='order_" + key +
                                "'><div class='header'><h1>Pedido " +
                                key +
                                "</h1><hr style=\"width: 200px\"></div><div class='body' id='b" +
                                key + "'></div>\
                                <div class='footer' id='f" + key + "'></div></div>");

                            $.each(orders, (k2, items) => {
                                var side_items = "";
                                $.each(Object.keys(items), (k3, okeys) => {
                                    if (okeys != "name" && okeys != "id" &&
                                        okeys !=
                                        "quantity" && okeys != "deadline") {
                                        side_items += items[okeys]['quantity'] +
                                            "x " +
                                            items[okeys]['name'] + " / ";
                                    }
                                })
                                side_items = side_items.slice(0, -2);
                                $("#b" + key).append("<div class=\"item\">\
                                    <span class=\"title\">" + items.quantity + "x " + items.name + ".</span>\
                                    <span class=\"sides\">" + side_items + "</span>\
                                    </div><hr style=\"width: 50vw\">");

                                let time = new Date(items.deadline);
                                $("#f" + key + " h5").remove();
                                $("#f" + key).append(
                                    "<h5 class=\"title\" style=\"float: right\">" +
                                    time
                                    .getHours() + ":" + time.getMinutes() + "</h5>");
                            })
                        }
                    });
                }
                ordersData = data;
            }



            // Draggable Cards
            $(function() {
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
                                $.ajax({
                                    method: "post",
                                    url: "/professional/fast_close",
                                    data: {
                                        "_token": "{{ csrf_token() }}",
                                        "id": this.id.replace(
                                            "order_", "")
                                    }
                                }).done((res) => {
                                    successToast(res.status, res
                                        .message);
                                }).fail((err) => {
                                    errorToast(err.responseJSON
                                        .status, err
                                        .responseJSON.message)
                                });
                                $(this).remove();
                            });
                        } else { // Animate the card back to its original position
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
        });
    });
</script>
