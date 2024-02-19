<div class="modal fade" id="modal-show-{{ $app->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                        Información de contacto
                    </div>
                    <ul class="list-group list-group-flush" align="center">
                        <li class="list-group-item">Id
                            <b>{{ $app->id }}</b>
                        </li>
                        <li class="list-group-item">Nombre de contacto
                            <b>{{ $app->nombre }}</b>
                        </li>
                        <li class="list-group-item">Apellido
                            <b>{{ $app->apellido }}</b>
                        </li>
                        <li class="list-group-item">Correo
                            <b>{{ $app->correo }}</b>
                        </li>
                        <li class="list-group-item">Telefono
                            <b>{{ $app->telefono }}</b>
                        </li>
                        <li class="list-group-item">Etiquetas
                            <b>
                                @foreach ($app->tags as $tag)
                                    <span
                                        style="background-color: {{ $tag->color }};
                                padding: 5px; border-radius: 4px;">{{ $tag->nombre }}</span>
                                @endforeach
                            </b>
                        </li>
                        <li class="list-group-item">Notas
                            <b>{{ $app->notas }}</b>
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
