<div class="modal fade" id="modal-edit-{{ $app->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Editar Permisos del Rol</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm-{{ $app->id }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="card" style="width: 100%;">
                        <div class="card-header bg-primary text-white" align="center">
                            Permisos del Rol para {{ $app->name }}
                        </div>
                        <div class="card-body">
                            @foreach ($permissions as $permission)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}" {{-- Cambia aquí el valor de id a name --}}
                                        {{ $app->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ $permission->name }} - {{ $permission->description }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
