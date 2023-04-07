{{-- Call this to get a confirm modal --}}
<style>
    .warn-icon {
        color: #dc3545;
        font-size: 100px;
    }
</style>

<div class="modal fade" id="{{ $modal_id }}" aria-labelledby="{{ $modal_id }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-3">
                        <i class="fa-solid fa-triangle-exclamation warn-icon"></i>
                    </div>
                    <div class="col-9">
                        <h5 class="modal-title" id="title">{{ $title }}</h5>
                        <span class="text-muted" id="warning">{{ $span }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{isset($dangerLBL) ? $dangerLBL : "Cancelar"}}</button>
                <button class="btn btn-primary" @if (isset($function_name)) onclick="{{ $function_name }}()" @endif
                    @if (isset($confirm_id)) id="{{ $confirm_id }}" @endif>Confirmar</button>
            </div>
            @if (isset($hidden))
                <input type="hidden" id="{{ $hidden }}"> @endif
                    </div>
            </div>
        </div>
