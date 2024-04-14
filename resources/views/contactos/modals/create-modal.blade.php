<div class="modal fade" id="createContactoModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="createFormContactos">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Crear Nuevo Contacto</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @csrf
                    {{-- estos son los datos que se deben llenar automaticamente --}}
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" class="form-control" id="apellido" name="apellido">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>
                    <div class="form-group">
                        <label for="telefono">Telefono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9]{12}" title="Digita el prefijo del país seguido del numero celular" required>
                        <small id="telefonoHelp" class="form-text text-muted">Ejemplo: 571234567890</small>
                    </div>
                    <div>
                        <select id="etiqueta" name="etiqueta[]" class="form-select form-control mb-3" multiple required>
                            <option value="">Selecciona una Etiqueta</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notas">Notas</label>
                        <input type="text" class="form-control" id="notas" name="notas">
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
