{{-- Tabs for settings page --}}

<style>
    .tabs {
        width: 20%;
        position: fixed;
        border-radius: 15px;
        box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.25);
        min-width: 5%;
    }

    .tabs>li {
        cursor: pointer;
    }

    .active::before {
        content: none;
    }

    .btn-activation {
        font-weight: 800;
    }

    .active {
        background-color: #1C46B2 !important;
        border-color: #1C46B2 !important;
    }

    .small-screen-tabs {
        display: none;
    }

    @media (max-width: 620px) {
        .small-screen-tabs {
            display: block;
        }

        .tabs {
            display: none;
        }
    }
</style>
<title>Configurações</title>
<div class="small-screen-tabs">
    @if (session()->get('type.edit_page') || session()->get('type.owner') || session()->get('type.admin'))
        <button class="btn {{ $tab == 'general' ? 'btn-activation' : '' }}"
            onclick="redirect('/professional/configuracoes/geral')">Geral</button>
    @endif
    <button class="btn {{ $tab == 'users' ? 'btn-activation' : '' }}"
        onclick="redirect('/configuracoes/user')">Pessoal</button>
    @if (session()->get('type.owner') || session()->get('type.admin'))
        <button class="btn {{ $tab == 'admin' ? 'btn-activation' : '' }}"
            onclick="redirect('/professional/configuracoes/admin')">Admin</button>
    @endif
    <button class="btn {{ $tab == 'dev' ? 'btn-activation' : '' }}"
        onclick="redirect('/developers/api')">Desenvolvedores</button>
</div>

<ul class="list-group tabs">
    @if (session()->get('type.edit_page') || session()->get('type.owner') || session()->get('type.admin'))
        <li onclick="redirect('/professional/configuracoes/geral')"
            class="list-group-item list-group-item-action {{ $tab == 'general' ? 'active' : '' }}">Geral</li>
    @endif
    <li onclick="redirect('/configuracoes/user')"
        class="list-group-item list-group-item-action {{ $tab == 'users' ? 'active' : '' }}">Pessoal</li>
    @if (session()->get('type.owner') || session()->get('type.admin'))
        <li onclick="redirect('/professional/configuracoes/admin')"
            class="list-group-item list-group-item-action {{ $tab == 'admin' ? 'active' : '' }}">Admin</li>
    @endif
    <li onclick="redirect('/developers/api')"
        class="list-group-item list-group-item-action {{ $tab == 'dev' ? 'active' : '' }}">Developers</li>
</ul>


<script>
    function redirect(link) {
        window.location.replace(link);
    }
</script>
