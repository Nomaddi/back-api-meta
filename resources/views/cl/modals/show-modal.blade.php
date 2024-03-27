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
                        Información de la solicitud
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Id
                            <b>{{ $app->id }}</b>
                        </li>
                        <li class="list-group-item">Empresa
                            <b>{{ $app->empresa }}</b>
                        </li>
                        <li class="list-group-item">Contrato
                            <b>{{ $app->contrato }}</b>
                        </li>
                        <li class="list-group-item">Codigo
                            <b>{{ $app->codigo_contrato }}</b>
                        </li>
                        <li class="list-group-item">Orden de servicio
                            <b>{{ $app->tipo_orden_id }}</b>
                        </li>
                        <li class="list-group-item">Descripción general
                            <b>{{ $app->desc_general_act }}</b>
                        </li>
                        <li class="list-group-item">Objeto
                            <b>{{ $app->objeto }}</b>
                        </li>
                        <li class="list-group-item">Fecha inicio
                            <b>{{ $app->fecha_inicio }}</b>
                        </li>
                        @if (!is_null($app->fecha_recibo))
                            <li class="list-group-item">Fecha recibo
                                <b>{{ $app->fecha_recibo }}</b>
                            </li>
                        @endif
                        @if (!is_null($app->hora_limite))
                            <li class="list-group-item">Hora limite
                                <b>{{ $app->hora_limite }}</b>
                            </li>
                        @endif
                        <li class="list-group-item">Ubicación de los trabajos
                            <b>
                                @php
                                    // Intenta decodificar la cadena JSON. Si falla, usa un array vacío como respaldo.
                                    $tagsArray = json_decode($app->tag, true) ?? [];
                                @endphp

                                {{-- Utiliza la directiva de Blade para colecciones con una verificación adicional --}}
                                {{ collect($tagsArray)->filter()->implode(', ') }}
                            </b>
                        </li>
                        <li class="list-group-item">Difundir en:
                            <b>
                                @php
                                    // Intenta decodificar la cadena JSON. Si falla, usa un array vacío como respaldo.
                                    $publicArray = json_decode($app->publicacion, true) ?? [];
                                @endphp

                                {{-- Utiliza la directiva de Blade para colecciones con una verificación adicional --}}
                                {{ collect($publicArray)->filter()->implode(', ') }}
                            </b>
                        </li>
                        <li class="list-group-item">Estado
                            <span
                                class="badge {{ $app->estado == 'publicado' ? 'badge-primary' : 'badge-danger' }} badge-pill">{{ $app->estado }}</span>
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
