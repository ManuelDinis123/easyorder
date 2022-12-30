@include('layouts.includes')

<style>
    .ico-err {
        position: relative;
        font-size: 300px;
        left: 300px;
        color: #dc3545;
    }

    h1 {
        position: relative;
        top: 20%;
        left: 200px;
    }

    a {
        position: relative;
        top: 20%;
        left: 200px;
        font-size: 24px;
        text-decoration: none;
    }
</style>

<div class="row">
    <div class="col-6">
        <h1>Esta pagina não existe ou não tem permissão para a visualizar</h1>
        <a href="/" class="text-muted"><i class="fa-solid fa-house"></i> Voltar</a>
    </div>
    <div class="col-6">
        <i class="fa-solid fa-cloud-exclamation ico-err"></i>
    </div>
</div>
