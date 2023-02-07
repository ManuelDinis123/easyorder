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
        <a href="/professional/configuracoes" class="nav-item"><i class="fa-solid fa-cart-shopping"></i></a>
        <i class="fa-regular fa-pipe nav-line"></i>
        <a href="/professional/configuracoes" class="nav-item"><i class="fa-solid fa-gear"></i></a>
        <a href="/professional/chat" class="nav-item"><i class="fa-solid fa-user"></i></a>
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
        $("#searchBar").on('click', () => {
            if(window.location.pathname=="/search") return;

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

        $("#searchBar").on('keypress', (e)=>{
            if (e.which == 13) {
                console.log($("#searchBar").val());
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
