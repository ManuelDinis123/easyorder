@include('layouts.includes')
<link rel="stylesheet" href="{{ asset('css/create_restaurant.css') }}">

<div class="form-container">
    <div class="form-card">
        <h3 style="text-align: center">Crie o seu Restaurante</h3>
        <label class="mt-3">Nome de Restaurante</label>
        <input type="text" class="form-control" placeholder="Nome do seu restaurante">
        <label class="mt-3">Descrição</label>
        <input type="textarea" class="form-control" placeholder="Descrição do seu restaurante">
        <label class="mt-3">Logo</label>
        <div class="row">
            <div class="col-9"><input type="text" class="form-control" placeholder="Logotipo do seu restaurante">
            </div>
            <div class="col-3">
                <form action="/professional/fileupload" class="dropzone" id="profile">
                    @csrf
                    <div class="dz-message" data-dz-message>
                        <i class="fa-solid fa-camera cam"></i>
                    </div>
                    <br />
                </form>
            </div>
        </div>
        <label>Tipo de serviço</label>
        <select class="form-control type" name="states[]" multiple="multiple">
            <option value="restaurant">Restaurante</option>
            <option value="restaurant">Take-Away</option>
            <option value="restaurant">Entrega ao domicilio</option>
        </select>
        <button class="btn btn-primary form-control mt-3">Criar</button>
        <button class="btn btn-danger form-control mt-2" onclick="onCancel()">Cancelar</button>
    </div>
</div>

<script>
    Dropzone.options.profile = {
        method: 'post',
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        uploadMultiple: false,
        maxFiles: 1,
        autoProcessQueue: false,
        previewsContainer: false,
    }

    function onCancel() {
        window.location.replace("/");
    }

    $(document).ready(function() {
        $('.type').select2();
    });
</script>
