<link rel="stylesheet" href="{{ asset('css/nav.css') }}">
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>EasyOrder</title>
</head>

<header class="header nav-extra" id="header">
    <div class="logo">
        <img src="{{ asset('img/eologo.svg') }}" id="eoLogo" style="cursor:pointer;">
    </div>
    <div class="inner-addon" id="inner-addon">
        <i class="fa-solid fa-magnifying-glass left-addon"></i>
        <input type="text" class="nav-search" id="searchBar" placeholder="Que restaurante procura?">
    </div>
    <div class="icons-nav">
        <a href="/carinho" class="nav-item position-relative shpcrt">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="position-absolute badge rounded-pill shp-badge" id="cart_total">
                0
            </span>
        </a>
        <i class="fa-regular fa-pipe nav-line"></i>
        <div class="dropdown">
            <a href="#" class="nav-item" id="userDropdown" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                {{-- <i class="fa-solid fa-user"></i> --}}
                <img src="{{ asset('img/pfp/' . session()->get('user.pfp')) }}"
                onerror="this.src = '{{ asset('img/pfp/defaultpfp.png') }}';" class="navbar-pfp n-icons"
                id="userIco">
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="/configuracoes/user"><i class="fa-regular fa-user"></i> Perfil</a></li>
                <li><a class="dropdown-item" href="/pedidos"><i class="fa-solid fa-moped"></i> Os Meus Pedidos</a></li>
                @if (!session()->get('user.isProfessional'))
                    <li><a class="dropdown-item" href="/novo/restaurante"><i class="fa-regular fa-user-tie"></i> Mudar
                            para
                            Pro</a></li>
                @else
                    <li><a class="dropdown-item" href="/professional"><i class="fa-solid fa-table-columns"></i> Ir para
                            Pro</a></li>
                @endif
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li id="logout"><a class="dropdown-item" href="#"><i
                            class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</header>

@include('components.frontend.load')

<div class="pt-3 visually-hidden" id="content">
    @yield('content')
</div>

</html>

<script>
    $(window).on('load', () => {
        $("#loading").fadeOut(500, function() {
            // fadeOut complete. Remove the loading div
            $("#loading").remove(); //makes page more lightweight
            $("#content").removeClass("visually-hidden");
        });
    })

    $(document).ready(() => {
        checkForNotifications("{{ csrf_token() }}");

        $("#logout").on('click', () => {
            $.ajax({
                method: 'post',
                url: '/logout',
                data: {
                    "_token": "{{ csrf_token() }}",
                }
            }).done(res => {
                window.location.replace(res);
            })
        })

        $.ajax({
            method: 'post',
            url: '/getCartItems',
            data: {
                "_token": "{{ csrf_token() }}",
            }
        }).done((res) => {
            var total = 0;
            if (res != 'no items found...') {
                $.each(res, (key, val) => {
                    total += val.quantity;
                })
            }
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
