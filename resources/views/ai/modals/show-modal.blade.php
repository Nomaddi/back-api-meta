<div class="modal fade" id="modal-show-{{ $app->id }}" tabindex="-1" role="dialog"aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Vista previa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <br>
                <div class="card" style="width: 100%;">
                    <div class="card-header bg-primary text-white" align="center">
                        Información de la aplicacion
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">ID:
                            <b>{{ $app->id }}</b>
                        </li>
                        <li class="list-group-item">Nombre:
                            <b>{{ $app->nombre }}</b>
                        </li>
                        <li class="list-group-item">Descripcion:
                            <b>{{ $app->descripcion }}</b>
                        </li>
                        <li class="list-group-item">API Key:
                            <b>{{ $app->api_key }}</b>
                        </li>
                        <li class="list-group-item">Aplicación:
                            <b>{{ $app->organizacion }}</b>
                        </li>
                        <li class="list-group-item">Asistente ID:
                            <b>{{ $app->asistente_id }}</b>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

{{--resources/views/ai/modals/show-modal.blade.php --}}
