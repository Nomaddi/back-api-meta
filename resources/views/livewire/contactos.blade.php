<div class="container">
    @section('content')
        <div class="row">
            <div class="col-lg-12 my-3">
                <div>
                    <a data-toggle="modal" data-target="#createContactoModal" class="btn btn-primary btn-sm mb-2"
                        title="Crear">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <a data-toggle="modal" data-target="#importAppModal" class="btn btn-success btn-sm mb-2" title="Importar">
                        <i class="fa fa-file-import"></i>
                    </a>
                    <a data-toggle="modal" data-target="#exportarAppModal" class="btn btn-success btn-sm mb-2"
                        title="Exportar">
                        <i class="fa fa-file-export"></i>
                    </a>
                </div>
            </div>
        </div>
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                    <ul>
                        <li>{{ $error }}</li>
                    </ul>
                @endforeach
            </div>
        @endif
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Contactos</h3>
                <button wire:click="create" class="mb-4 px-4 py-2 bg-green-500 text-white rounded">
                    Nuevo Barrio
                </button>
            </div>
            <div class="card-body">
                <!-- Más contenido aquí -->
                <div>
                    @livewire('contacto-table')
                </div>
            </div>
            {{-- @include('./livewire.contactos.modals.crud-modal') --}}
            @include('./livewire.contactos.modals.create-modal')
            {{-- @include('./livewire.contactos.modals.delete-modal') --}}
            @include('./livewire.contactos.modals.import-modal')
            @include('./livewire.contactos.modals.export-modal')
        </div>

        <!-- Modal para Editar -->
        @if ($showEditModal)
            <div class="modal fade show" style="display: block;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Contacto</h5>
                            <button class="btn-close" wire:click="$set('showEditModal', false)"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" wire:model.defer="nombre" class="form-control mb-2" placeholder="Nombre">
                            <input type="text" wire:model.defer="apellido" class="form-control mb-2"
                                placeholder="Apellido">
                            <input type="email" wire:model.defer="correo" class="form-control mb-2" placeholder="Correo">
                            <input type="text" wire:model.defer="telefono" class="form-control mb-2"
                                placeholder="Teléfono">
                            <textarea wire:model.defer="notas" class="form-control mb-2" placeholder="Notas"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('showEditModal', false)">Cancelar</button>
                            <button class="btn btn-primary" wire:click="update">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal para Eliminar -->
        @if ($showDeleteModal)
            <div class="modal fade show" style="display: block;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Eliminar Contacto</h5>
                            <button class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de eliminar este contacto?</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancelar</button>
                            <button class="btn btn-danger" wire:click="delete">Eliminar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endsection
</div>
