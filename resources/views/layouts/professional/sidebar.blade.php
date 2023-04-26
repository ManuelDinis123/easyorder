<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>

<body id="body-pd">
    <header class="header nav-extra" id="header">
        <div class="header_toggle">
            <i class="fa-light fa-bars hToggle" id="header-toggle"></i>
        </div>
        <div class="ms-auto icons-nav">
            {{-- Custom User Dropdown --}}
            <div class="dpDown">
                <img src="{{ asset('img/pfp/' . session()->get('user.pfp')) }}"
                    onerror="this.src = '{{ asset('img/pfp/defaultpfp.png') }}';" class="navbar-pfp n-icons"
                    id="userIco">
                <div class="custom-dpdown custom-dpdown-open hide-dpDown" id="userDp">
                    <div class="custom-dpdown-header">
                        <span
                            class="custom-dpdown-title">{{ session()->get('user')['firstName'] . ' ' . session()->get('user')['lastName'] }}</span>
                    </div>
                    <div class="custom-dpdown-body">
                        <span class="custom-dpdown-item-container"><a class="custom-dpdown-item"
                                href="/professional/configuracoes/user"><i class="fa-solid fa-user-gear"></i>
                                Configurações</a></span>
                        <span class="custom-dpdown-item-container"><a class="custom-dpdown-item" href="/"><i
                                    class="fa-solid fa-arrow-up-right-from-square"></i> EasyOrder</a></span>
                        <span class="custom-dpdown-item-container leave"><span class="custom-dpdown-item"
                                href="#"><i class="fa-solid fa-door-open"></i> LogOut</span></span>
                    </div>
                </div>
            </div>
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
                    <a id="view_orders_side" href="/professional/encomendas"
                        class="nav_link {{ $file == 'orders' ? 'active' : '' }} 
                        {{ !session()->get('type.owner') ? (!session()->get('type.admin') ? (!session()->get('type.view_orders') ? 'visually-hidden' : '') : '') : '' }}">
                        <i class="fa-solid fa-ballot-check nav_icon"></i>
                        <span class="nav_name">Encomendas</span>
                    </a>
                    <a id="view_menu_side" href="/professional/ementa"
                        class="nav_link {{ $file == 'menu' ? 'active' : '' }}
                        {{ !session()->get('type.owner') ? (!session()->get('type.admin') ? (!session()->get('type.view_menu') ? 'visually-hidden' : '') : '') : '' }}">
                        <i class="fa-solid fa-burger-cheese"></i>
                        <span class="nav_name">Ementa</span>
                    </a>
                    <a id="view_editpage_side" href="/professional/conteudo"
                        class="nav_link {{ $file == 'editpage' ? 'active' : '' }}
                        {{ !session()->get('type.owner') ? (!session()->get('type.admin') ? (!session()->get('type.edit_page') ? 'visually-hidden' : '') : '') : '' }}">
                        <i class="fa-duotone fa-pen-ruler"></i>
                        <span class="nav_name">Gerir Conteúdo</span>
                    </a>
                    <a id="view_editpage_side" href="/professional/criticas"
                        class="nav_link {{ $file == 'reviews' ? 'active' : '' }}
                        {{ !session()->get('type.owner') ? (!session()->get('type.admin') ? (!session()->get('type.view_stats') ? 'visually-hidden' : '') : '') : '' }}">
                        <i class="fa-sharp fa-solid fa-star"></i>
                        <span class="nav_name">Ver Reviews</span>
                    </a>
                    <a id="view_stats_side" href="/professional/stats"
                        class="nav_link {{ $file == 'stats' ? 'active' : '' }}
                        {{ !session()->get('type.owner') ? (!session()->get('type.admin') ? (!session()->get('type.view_stats') ? 'visually-hidden' : '') : '') : '' }}">
                        <i class="fa-solid fa-chart-line-up nav_icon"></i>
                        <span class="nav_name">Estatísticas</span>
                    </a>
                    <div id="dp"
                        class="dropdown {{ session()->get('type.owner') || session()->get('type.admin') || session()->get('type.invite_users') || session()->get('type.ban_users') ? '' : 'visually-hidden' }}">
                        <a href="#" class="nav_drop hToggle" data-bs-toggle="dropdown" id="sidebarDropDown">
                            <i class="fa-sharp fa-solid fa-toolbox nav_icon"></i>
                            <span class="nav_name dropdown-toggle">Administração</span>
                        </a>
                        <ul class="dropdown-menu n-dropdown animate slideIn" id="dpmenu"
                            aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item activate {{ $file == 'edit_users' ? 'li_on' : '' }}"
                                    href="/professional/admin/users"><i class="fa-solid fa-users-gear"></i>
                                    Utilizadores</a></li>
                            <li id="dpl2"
                                class="{{ session()->get('type.owner') || session()->get('type.admin') ? '' : 'visually-hidden' }}">
                                <a class="dropdown-item activate {{ $file == 'perms' ? 'li_on' : '' }}"
                                    href="/professional/admin/permissions"><i class="fa-solid fa-lock"></i>
                                    Permissões</a>
                            </li>
                            <li id="dpl3"
                                class="{{ session()->get('type.owner') || session()->get('type.admin') ? '' : 'visually-hidden' }}">
                                <a class="dropdown-item activate {{ $file == 'options' ? 'li_on' : '' }}"
                                    href="/professional/admin/options"><i class="fa-solid fa-gear"></i>
                                    Opções</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <span id="leave" class="nav_link leave" style="cursor: pointer">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="nav_name">SignOut</span>
            </span>
        </nav>
    </div>

    @if (!session()->get('restaurant.isPublic') && (session()->get('type.owner') || session()->get('type.admin')))
        <button class="btn btn-primary publish-btn" onclick="publish()">Publicar Restaurante</button>
    @endif

    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

    @include('components.loader')

    <div class="height-100 pt-3 visually-hidden" id="content">
        @yield('content')
    </div>
    <script>
        // function to publish restaurant        
        function publish() {
            $.ajax({
                method: 'post',
                url: '/publish',
                data: {
                    '_token': "{{ csrf_token() }}"
                }
            }).done((res) => {
                successToast(res.title, res.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }

        $(window).on('load', () => {
            $("#loading").fadeOut(500, function() {
                // fadeOut complete. Remove the loading div
                $("#loading").remove(); //makes page more lightweight 
                $("#content").removeClass("visually-hidden");
            });
        })

        // toggle the sidebar
        function toggleSide() {
            sidebarIsOpened = !sidebarIsOpened;
            $("#nav-bar").toggleClass("show_side");
            $("#header-toggle").toggleClass("fa-xmark");
            $("#body-pd").toggleClass("body-pd");
            $("#header").toggleClass("body-pd");
        }

        $(document).ready(() => {
            checkForNotifications("{{ csrf_token() }}");
            sidebarIsOpened = true;
            $(".hToggle").on('click', function() {
                if (this.id == "sidebarDropDown") {
                    if (!$("#" + this.id).hasClass("show") && !sidebarIsOpened) return;
                    toggleSide();
                } else {
                    toggleSide();
                }
            });

            $(".leave").on('click', () => {
                $.ajax({
                    method: 'post',
                    url: '/logout',
                    data: {
                        "_token": $('#token').val(),
                    }
                }).done(res => {
                    window.location.replace(res);
                })
            })

            // Hide the dropdowns when clicking outside of them
            $(document).on('click', function(event) {
                var $trigger = $(".dpDown");
                if ($trigger !== event.target && !$trigger.has(event.target).length) {
                    $(".custom-dpdown-open").addClass('hide-dpDown');
                }
            })

            // dropdowns for navbar
            var map = ['user', 'bell']

            $.each(map, (key, val) => {
                $("#" + val + "Ico").on('click', () => {
                    if ($("#" + val + "Dp").hasClass('hide-dpDown')) {
                        $(".custom-dpdown-open").addClass('hide-dpDown');
                        $("#" + val + "Dp").removeClass('hide-dpDown');
                    } else {
                        $("#" + val + "Dp").addClass('hide-dpDown');
                    }
                })
            })

            $.ajax({
                method: 'post',
                url: '/update_session',
                data: {
                    "_token": "{{ csrf_token() }}"
                }
            }).done((res) => {
                if (res == 0) return;
                $.each(res.newSession, (key, val) => {
                    if (val == 0) {
                        $("#" + key + "_side").addClass('visually-hidden');
                    } else {
                        $("#" + key + "_side").removeClass('visually-hidden');
                    }
                })
                if (res.newSession.admin == 0 && res.newSession.owner == 0) {
                    $("#dp").addClass('visually-hidden');
                } else {
                    $("#view_menu_side").removeClass('visually-hidden');
                    $("#view_editpage_side").removeClass('visually-hidden');
                    $("#view_orders_side").removeClass('visually-hidden');
                    $("#view_stats_side").removeClass('visually-hidden');
                    $("#dp").removeClass('visually-hidden');
                }
                if (res.newSession.ban_users == 1 || res.newSession.invite_users == 1) {
                    $("#dp").removeClass('visually-hidden');
                }
                setTimeout(() => {
                    iziToast.warning({
                        title: res.title,
                        message: res.message,
                        icon: "fa-solid fa-triangle-exclamation"
                    });
                }, 800);
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        });
    </script>
</body>

</html>
