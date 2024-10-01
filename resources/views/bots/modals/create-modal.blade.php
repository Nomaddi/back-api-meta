<div class="modal fade" id="createBotModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createForm">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Nuevo Bot</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="nombre">Nombre del Bot</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                    </div>
                    <div class="form-group">
                        <label for="openai_key">OpenAI Key</label>
                        <input type="text" class="form-control" id="openai_key" name="openai_key" required>
                    </div>
                    <div class="form-group">
                        <label for="openai_org">OpenAI Org</label>
                        <input type="text" class="form-control" id="openai_org" name="openai_org" required>
                    </div>
                    <div class="form-group">
                        <label for="openai_assistant">OpenAI Assistant</label>
                        <input type="text" class="form-control" id="openai_assistant" name="openai_assistant" required>
                    </div>
                    <div class="form-group">
                        <label for="aplicacion_id">Aplicación</label>
                        <select class="form-control" id="aplicacion_id" name="aplicacion_id" required>
                            <option value="">Selecciona una aplicación</option>
                            @foreach ($aplicaciones as $aplicacion)
                                <option value="{{ $aplicacion->id }}">{{ $aplicacion->nombre }}</option>
                            @endforeach
                        </select>
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
