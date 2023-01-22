@include('layouts.includes')
<link rel="stylesheet" href="{{ asset('css/create_restaurant.css') }}">

<div class="form-container">
    <div class="form-card">
        <h3 style="text-align: center">Crie o seu Restaurante</h3>
        <label class="mt-3">Nome de Restaurante</label>
        <input type="text" class="form-control" placeholder="Nome do seu restaurante" id="name" autocomplete="off">
        <label class="mt-3">Descrição</label><br />
        <span class="text-muted">(optional)</span>
        <textarea class="form-control" placeholder="Descrição do seu restaurante" maxlength="180" rows="1"
            id="description"></textarea>
        <label class="mt-3">Logo</label><br />
        <span class="text-muted">(optional)</span>
        <div class="row g-0">
            <div class="col-10">
                <input type="text" class="form-control" placeholder="URL ou Upload" id="imageUrl" autocomplete="off">
            </div>
            <div class="col-2">
                <label for="imgFile" class="filePut">
                    <i class="fa-sharp fa-solid fa-camera"></i>
                    <input type="file" id="imgFile" style="display: none">
                </label>
            </div>
        </div>
        <label class="mt-3">Tipo de serviço</label>
        <select class="form-control type" name="types[]" multiple="multiple" id="slcType">
            <option value="restaurant">Restaurante</option>
            <option value="takeaway">Take-Away</option>
            <option value="delivery">Entrega ao domicilio</option>
        </select>
        <button class="btn btn-primary form-control mt-3" id="create">Criar</button>
        <button class="btn btn-danger form-control mt-2" onclick="onCancel()">Cancelar</button>
    </div>
</div>

<script>
    function onCancel() {
        window.location.replace("/");
    }

    $(document).ready(function() {
        $('.type').select2();

        imgFile = null;
        $('#imgFile').on('change', function() {
            var file = this.files[0];            
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function() {
                $(".fa-sharp").removeClass("fa-camera");
                $(".fa-sharp").addClass("fa-check");
                var base64 = reader.result;
                imgFile = {
                    "dataURL": base64,
                    "type": file.type
                };
            };
        });


        $("#create").on('click', () => {            
            // animate error
            map = ["name", "description", "slcType"]
            hasEmpty = animateErr(map);
            
            if(hasEmpty) return;

            // Get values                        
            values = {
                "name": $("#name").val(),
                "description": $("#description").val(),
                "type": $('.type').select2('val'),
                "imageUrl": $("#imageUrl").val(),
                "file": imgFile,
            }            

            $.ajax({
                method: 'post',
                url: '/novo/create',
                data: {
                    "_token": "{{csrf_token()}}",
                    "values": values
                }
            }).done((res)=>{
                if(res.title == "Erro") {
                    errorToast(res.title, res.message);
                } else {
                    window.location.replace("/professional");
                }
            })
        })
    });
</script>