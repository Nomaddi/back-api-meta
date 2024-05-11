<div class="modal fade" id="modal-edit-{{ $app->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Vista previa</h5>
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
                            Seleccione los roles para el usuario {{ $app->name }}
                        </div>
                        <div class="card-body">
                            @foreach ($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        value="{{ $role->name }}" {{ $app->hasRole($role->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role-{{ $role->id }}">
                                        {{ $role->name }}
                                    </label>
                                </div>
                            @endforeach
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
