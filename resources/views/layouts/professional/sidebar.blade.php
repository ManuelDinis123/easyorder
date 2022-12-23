<link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body id="body-pd">

    <header class="header nav-extra" id="header">
        <div class="header_toggle">
            <i class="fa-light fa-bars" id="header-toggle"></i>
        </div>
        <div class="ms-auto">
            <i class="fa-regular fa-gear n-icons"></i>
            <i class="fa-regular fa-message-lines n-icons"></i>
            <i class="fa-regular fa-bell n-icons"></i>
            <i class="fa-regular fa-user n-icons"></i>
            <a href="/home" style="color: black">
                <i class="fa-regular fa-arrow-up-right-from-square n-icons"></i>
            </a>
        </div>
    </header>

    <div class="l-navbar main-header" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="/professional" class="nav_logo">
                    <img src="{{ asset('img/eologo.svg') }}" class="eologo img-fluid">
                    <span class="nav_logo-name">Easy Order</span>
                </a>
                <hr class="nav-separation-line" style="height: 2px;">
                <div class="nav_list">
                    <a href="/professional" class="nav_link {{ $file == 'dashboard' ? 'active' : '' }}">
                        <i class="fa-solid fa-house nav_icon"></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="/professional/encomendas" class="nav_link {{ $file == 'orders' ? 'active' : '' }}">
                        <i class="fa-solid fa-ballot-check nav_icon"></i>
                        <span class="nav_name">Encomendas</span>
                    </a>
                    <a href="/professional/ementa" class="nav_link {{ $file == 'menu' ? 'active' : '' }}">
                        <i class="fa-solid fa-burger-cheese nav_icon"></i>
                        <span class="nav_name">Ementa</span>
                    </a>
                    <a href="/professional/stats" class="nav_link {{ $file == 'stats' ? 'active' : '' }}">
                        <i class="fa-solid fa-chart-line-up nav_icon"></i>
                        <span class="nav_name">Estatísticas</span>
                    </a>
                    <div id="dp" class="dropdown">
                        <a href="#" class="nav_drop" data-bs-toggle="dropdown">
                            <i class="fa-sharp fa-solid fa-toolbox nav_icon"></i>
                            <span class="nav_name dropdown-toggle">Administração</span>
                        </a>
                        <ul class="dropdown-menu n-dropdown" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item activate {{ $file == 'edit_users' ? 'li_on' : '' }}"
                                    href="/professional/admin/users"><i class="fa-solid fa-users-gear"></i>
                                    Utilizadores</a></li>
                            <li><a class="dropdown-item activate {{ $file == 'perms' ? 'li_on' : '' }}"
                                    href="/professional/admin/permissions"><i class="fa-solid fa-lock"></i>
                                    Permissões</a></li>
                            <li><a class="dropdown-item activate {{ $file == 'options' ? 'li_on' : '' }}"
                                    href="/professional/admin/options"><i class="fa-solid fa-gear"></i>
                                    Opções</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <a href="/" class="nav_link">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="nav_name">SignOut</span>
            </a>
        </nav>
    </div>
    <div class="height-100 bg-light">
        @yield('content')
    </div>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId);

                // Validate that all variables exist
                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener("click", () => {
                        // show navbar
                        nav.classList.toggle("show");
                        // change icon
                        toggle.classList.toggle("fa-xmark");
                        // add padding to body
                        bodypd.classList.toggle("body-pd");
                        // add padding to header
                        headerpd.classList.toggle("body-pd");
                    });
                }
            };

            showNavbar("header-toggle", "nav-bar", "body-pd", "header");

        });
    </script>
</body>

</html>
