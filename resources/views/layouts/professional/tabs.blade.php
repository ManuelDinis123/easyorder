{{-- Tabs for settings page --}}

<style>
    .tabs {
        width: 20%;
        position: fixed;
        border-radius: 15px;
        box-shadow: 0px 1px 10px rgba(0, 0, 0, 0.25);
    }

    .tabs>li {
        cursor: pointer;
    }

    .active::before {
        content: none;
    }

    .active {
        background-color: #1C46B2 !important;
        border-color: #1C46B2 !important;
    }
</style>

<ul class="list-group tabs">
    @if ( session()->get('type.edit_page') || session()->get('type.owner') || session()->get('type.admin'))
        <li onclick="redirect('/professional/configuracoes/geral')"
            class="list-group-item list-group-item-action {{ $tab == 'general' ? 'active' : '' }}">Geral</li>
    @endif
    <li onclick="redirect('/professional/configuracoes/user')"
        class="list-group-item list-group-item-action {{ $tab == 'users' ? 'active' : '' }}">Pessoal</li>
    @if (session()->get('type.owner') || session()->get('type.admin'))
        <li onclick="redirect('/professional/configuracoes/admin')"
            class="list-group-item list-group-item-action {{ $tab == 'admin' ? 'active' : '' }}">Admin</li>
    @endif
</ul>


<script>
    function redirect(link) {
        window.location.replace(link);
    }
</script>
