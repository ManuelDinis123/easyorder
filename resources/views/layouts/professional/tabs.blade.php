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
        content: none
    }
</style>


<ul class="list-group tabs">
    <li onclick="redirect('/professional/configuracoes/geral')" class="list-group-item list-group-item-action {{ $tab == 'general' ? 'active' : '' }}">Geral</li>
    <li onclick="redirect('/professional/configuracoes/user')" class="list-group-item list-group-item-action {{ $tab == 'users' ? 'active' : '' }}">Utilizador</li>
    <li onclick="redirect('/professional/configuracoes/restaurante')" class="list-group-item list-group-item-action {{ $tab == 'restaurant' ? 'active' : '' }}">Restaurante</li>
    <li onclick="redirect('/professional/configuracoes/conexoes')" class="list-group-item list-group-item-action {{ $tab == 'connections' ? 'active' : '' }}">Conexões</li>
</ul>


<script>
    function redirect(link) {
        window.location.replace(link);
    }
</script>
