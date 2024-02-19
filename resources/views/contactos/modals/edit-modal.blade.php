<div class="modal fade" id="modal-edit-{{ $app->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Editar contacto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm-{{ $app->id }}">
                @csrf
                @method('PUT') {{-- Este método será simulado por AJAX --}}
                <div class="modal-body">
                    <br>
                    <div class="card" style="width: 100%;">
                        <div class="card-header bg-primary text-white" align="center">
                            Información de contacto
                        </div>
                        <div class="form-group">
                            <label for="nombre-{{ $app->id }}">Nombre</label>
                            <input type="text" class="form-control" id="nombre-{{ $app->id }}" name="nombre"
                                value="{{ $app->nombre }}" required>
                        </div>
                        <div class="form-group">
                            <label for="apellido-{{ $app->id }}">Apellido</label>
                            <input type="text" class="form-control" id="apellido-{{ $app->id }}" name="apellido"
                                value="{{ $app->apellido }}" required>
                        </div>
                        <div class="form-group">
                            <label for="correo-{{ $app->id }}">Correo</label>
                            <input type="email" class="form-control" id="correo-{{ $app->id }}" name="correo"
                                value="{{ $app->correo }}" required>
                        </div>
                        <div class="form-group">
                            <label for="telefono-{{ $app->id }}">Telefono</label>
                            <input type="number" class="form-control" id="telefono-{{ $app->id }}"
                                name="telefono" value="{{ $app->telefono }}" required>
                        </div>
                        <div>
                            <select id="etiqueta-{{ $app->id }}" name="etiqueta[]"
                                class="form-select form-control mb-3" multiple required>
                                <option value="">Selecciona una Etiqueta</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}"
                                        @if (in_array($tag->id, $app->tags->pluck('id')->toArray())) selected @endif>
                                        {{ $tag->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notas">Notas</label>
                            <input type="text" class="form-control" id="notas-{{ $app->id }}" name="notas"
                                value="{{ $app->notas }}" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Guardar cambios</button>
                    </div>
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
