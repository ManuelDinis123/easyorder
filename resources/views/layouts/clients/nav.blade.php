<link rel="stylesheet" href="{{ asset('css/nav.css') }}">

<header class="header nav-extra" id="header">
    <div class="logo">
        <img src="{{ asset('img/eologo.svg') }}" id="eoLogo" style="cursor:pointer;">
    </div>
    <div class="inner-addon" id="inner-addon">
        <i class="fa-solid fa-magnifying-glass left-addon"></i>
        <input type="text" class="nav-search" id="searchBar" placeholder="Que restaurante procura?">
    </div>
    <div class="icons-nav">
        <a href="/carrinho" class="nav-item position-relative">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="position-absolute badge rounded-pill shp-badge" id="cart_total">
                0
            </span>
        </a>
        <i class="fa-regular fa-pipe nav-line"></i>
        <a href="#" class="nav-item"><i class="fa-solid fa-gear"></i></a>
        <a href="#" class="nav-item"><i class="fa-solid fa-user"></i></a>
    </div>
</header>

@include('components.frontend.load')

<div class="pt-3 visually-hidden" id="content">
    @yield('content')
</div>

<script>
    $(window).on('load', () => {
        $("#loading").fadeOut(500, function() {
            // fadeOut complete. Remove the loading div
            $("#loading").remove(); //makes page more lightweight
            $("#content").removeClass("visually-hidden");
        });
    })

    $(document).ready(() => {
        $.ajax({
            method: 'post',
            url: '/getCartItems',
            data: {
                "_token": "{{ csrf_token() }}",
            }
        }).done((res) => {
            var total = 0;
            $.each(res, (key, val) => {
                total += val.quantity;
            })
            $("#cart_total").text(total);
        })


        $("#searchBar").on('click', () => {
            if (window.location.pathname == "/search") return;

            $.ajax({
                method: 'post',
                url: '/search_no_reload',
                data: {
                    '_token': "{{ csrf_token() }}"
                }
            }).done((res) => {
                $("#content").html(res);
                window.history.replaceState(null, null, "/search");
            })
        });

        $("#searchBar").on('keypress', (e) => {
            if (e.which == 13) {
                $.ajax({
                    method: "post",
                    url: '/search_confirm',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        query: $("#searchBar").val()
                    }
                }).done((res) => {
                    $("#container").html(res);
                })
                $("#inner-addon").addClass("animate-search");
                setTimeout(() => {
                    $("#inner-addon").removeClass("animate-search");
                }, 100);
            }
        })

        $("#eoLogo").on('click', () => {
            window.location.href = "/";
        })
    })
</script>
