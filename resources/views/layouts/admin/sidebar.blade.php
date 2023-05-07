<link rel="stylesheet" href="{{ asset('css/adminsidebar.css') }}">
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>EasyOrder Admin</title>
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
                                href="/configuracoes/user"><i class="fa-solid fa-user-gear"></i>
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
                <a href="/admin" class="nav_logo">
                    <img src="{{ asset('img/eologo.svg') }}" class="eologo img-fluid">
                    <span class="nav_logo-name">Easy Order</span>
                </a>
                <hr class="nav-separation-line" style="height: 2px;">
                <div class="nav_list">
                    <a href="/admin/dashboard" class="nav_link {{ $file == 'dashboard' ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge-low"></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="/admin/restaurantes" class="nav_link {{ $file == 'restaurants' ? 'active' : '' }}">
                        <i class="fa-solid fa-utensils"></i>
                        <span class="nav_name">Restaurantes</span>
                    </a>
                    <a href="/admin/users" class="nav_link {{ $file == 'users' ? 'active' : '' }}">
                        <i class="fa-solid fa-user"></i>
                        <span class="nav_name">Users</span>
                    </a>
                    <a href="/admin/denuncias" class="nav_link {{ $file == 'reports' ? 'active' : '' }}">
                        <i class="fa-sharp fa-solid fa-flag"></i>
                        <span class="nav_name">Denúncias</span>
                    </a>
                </div>
            </div>
            <span id="leave" class="nav_link leave" style="cursor: pointer">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="nav_name">Sair</span>
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
        });
    </script>
</body>

</html>
