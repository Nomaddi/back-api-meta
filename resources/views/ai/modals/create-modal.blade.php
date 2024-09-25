<div class="modal fade" id="createAppModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createForm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Crear Nuevo numero</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    {{-- estos son los datos que se deben llenar automaticamente --}}
                    <div class="form-group">
                        <label for="nombre">Nombre de la ai</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">descripcion de la ai</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="api_key">api_key</label>
                        <input type="text" class="form-control" id="api_key" name="api_key" required>
                    </div>
                    <div class="form-group">
                        <label for="organizacion">organizacion</label>
                        <input type="text" class="form-control" id="organizacion" name="organizacion" required>
                    </div>
                    <div class="form-group">
                        <label for="asistente_id">asistente_id</label>
                        <input type="text" class="form-control" id="asistente_id" name="asistente_id" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- resources/views/ai/modals/create-modal.blade.php --}}
