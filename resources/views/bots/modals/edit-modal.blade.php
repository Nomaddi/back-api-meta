<div class="modal fade" id="modal-edit-{{ $bot->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"> resource/views/bot/modals/edit-modal.blade.php
            <form id="editForm-{{ $bot->id }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Bot</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nombre-{{ $bot->id }}">Nombre del Bot</label>
                        <input type="text" class="form-control" id="nombre-{{ $bot->id }}" name="nombre" value="{{ $bot->nombre }}" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion-{{ $bot->id }}">Descripción</label>
                        <input type="text" class="form-control" id="descripcion-{{ $bot->id }}" name="descripcion" value="{{ $bot->descripcion }}" required>
                    </div>
                    <div class="form-group">
                        <label for="openai_key-{{ $bot->id }}">OpenAI Key</label>
                        <input type="text" class="form-control" id="openai_key-{{ $bot->id }}" name="openai_key" value="{{ $bot->openai_key }}" required>
                    </div>
                    <div class="form-group">
                        <label for="openai_org-{{ $bot->id }}">OpenAI Org</label>
                        <input type="text" class="form-control" id="openai_org-{{ $bot->id }}" name="openai_org" value="{{ $bot->openai_org }}" required>
                    </div>
                    <div class="form-group">
                        <label for="openai_assistant-{{ $bot->id }}">OpenAI Assistant</label>
                        <input type="text" class="form-control" id="openai_assistant-{{ $bot->id }}" name="openai_assistant" value="{{ $bot->openai_assistant }}" required>
                    </div>

                    {{-- Lista desplegable para seleccionar la aplicación --}}
                    <div class="form-group">
                        <label for="aplicacion_id-{{ $bot->id }}">Aplicación Asociada</label>
                        <select class="form-control" id="aplicacion_id-{{ $bot->id }}" name="aplicacion_id" required>
                            <option value="">Selecciona una aplicación</option>
                            @foreach ($aplicaciones as $aplicacion)
                                <option value="{{ $aplicacion->id }}"
                                    {{ $bot->aplicaciones->first() && $bot->aplicaciones->first()->id == $aplicacion->id ? 'selected' : '' }}>
                                    {{ $aplicacion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
            {{-- boton consuatr metodo de recuperar informacion de asistente --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="getAssistantInfo('{{ $bot->openai_assistant }}')">Recuperar información del asistente</button>
            </div>

            <!-- Contenedor para mostrar la información del asistente -->
<div class="modal-body">
    <div id="assistant-info-container-{{ $bot->id }}">
        <!-- Aquí se insertará la tabla con la información del asistente -->
    </div>
</div>
        </div>
    </div>
</div>
