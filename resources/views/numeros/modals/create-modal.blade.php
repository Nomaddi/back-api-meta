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
                {{-- aqui quiero que valla el select --}}
                <select id="selectAplicacion" class="form-select form-control mb-3">
                    <option value="">Selecciona una Aplicación</option>
                    @foreach ($aplicaciones as $aplicacion)
                        <option value="{{ $aplicacion->id }}" data-token="{{ $aplicacion->token_api }}"
                            data-business="{{ $aplicacion->id_c_business }}">{{ $aplicacion->nombre }}</option>
                    @endforeach
                </select>
                <div id="tarjetasNumeros" class="d-flex flex-wrap">
                    <!-- Las tarjetas se insertarán aquí -->
                </div>
                <div class="modal-body">
                    @csrf
                    {{-- estos son los datos que se deben llenar automaticamente --}}
                    <div class="form-group">
                        <label for="nombre">Nombre de la Aplicación</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="numero">Numero de telefono</label>
                        <input type="text" class="form-control" id="numero" name="numero" required>
                    </div>
                    <div class="form-group">
                        <label for="id_telefono">ID telefono</label>
                        <input type="text" class="form-control" id="id_telefono" name="id_telefono" required>
                    </div>
                    <div class="form-group">
                        {{-- <label for="aplicacion_id">Nombre de aplicación</label> --}}
                        <input type="hidden" class="form-control" id="aplicacion_id" name="aplicacion_id" required>
                    </div>
                    <div class="form-group">
                        <label for="calidad">Calidad</label>
                        <input type="text" class="form-control" id="calidad" name="calidad" required>
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
