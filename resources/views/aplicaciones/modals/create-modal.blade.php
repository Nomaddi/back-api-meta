<div class="modal fade" id="createAppsModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createForm">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nueva Aplicación</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre de la Aplicación</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="id_app">ID de Aplicación</label>
                        <input type="text" class="form-control" id="id_app" name="id_app" required>
                    </div>
                    <div class="form-group">
                        <label for="id_c_business">ID Cuenta Bussines</label>
                        <input type="text" class="form-control" id="id_c_business" name="id_c_business" required>
                    </div>
                    <div class="form-group">
                        <label for="token_api">Token</label>
                        <input type="text" class="form-control" id="token_api" name="token_api" required>
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
