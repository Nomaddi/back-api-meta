<div class="modal fade" id="createBotModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- modal-lg para hacerlo más grande -->
        <div class="modal-content">
            <form id="createForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Crear Bot</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Campo oculto para el ID del bot -->
                <input type="hidden" id="botId" name="bot_id">
                <div class="modal-body">
                    <div class="row">
                        <!-- Primera columna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editBotName">Nombre del Bot</label>
                                <input type="text" class="form-control form-control-sm" id="editBotName"
                                    name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="editBotDescription">Descripción</label>
                                <input type="text" class="form-control form-control-sm" id="editBotDescription"
                                    name="descripcion">
                            </div>
                            <div class="form-group">
                                <label for="editOpenAIKey">OpenAI Key</label>
                                <input type="text" class="form-control form-control-sm" id="editOpenAIKey"
                                    name="openai_key" required>
                            </div>
                            <div class="form-group">
                                <label for="editOpenAIOrg">OpenAI Org</label>
                                <input type="text" class="form-control form-control-sm" id="editOpenAIOrg"
                                    name="openai_org" required>
                            </div>

                            {{-- input para perdi varios archivos --}}
                            <div class="form-group">
                                <label for="archivos">Seleccionar Archivos</label>
                                <input type="file" class="form-control" id="archivos" name="archivos[]" multiple
                                    accept=".c,.cpp,.cs,.css,.doc,.docx,.go,.html,.java,.js,.json,.md,.pdf,.php,.pptx,.py,.rb,.sh,.tex,.ts,.txt">
                            </div>

                            <!-- Lista desplegable para seleccionar la aplicación -->
                            <div class="form-group">
                                <label for="editAplicacionId">Desea agregarla a una App de WhatsApp?</label>
                                <select class="form-control form-control-sm" id="editAplicacionId" name="aplicacion_id">
                                    <option value="">Selecciona una aplicación</option>
                                    @foreach ($aplicaciones as $aplicacion)
                                        <option value="{{ $aplicacion->id }}">{{ $aplicacion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <!-- Segunda columna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editAssistantInstructions">Instrucciones</label>
                                <textarea class="form-control form-control-sm" id="editAssistantInstructions" name="instrucciones" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="editModel">Modelo</label>
                                <select class="form-control form-control-sm" id="editModel" name="modelo" required>
                                    <option value="gpt-4o-mini">gpt-4o-mini</option>
                                    <option value="gpt-4o">gpt-4o</option>
                                    <option value="gpt-4-turbo">gpt-4-turbo</option>
                                    <option value="gpt-4">gpt-4</option>
                                    <option value="gpt-3.5-turbo">gpt-3.5-turbo</option>
                                    <option value="gpt-4o-mini-2024-07-18">gpt-4o-mini-2024-07-18</option>
                                    <option value="gpt-4o-2024-08-06">gpt-4o-2024-08-06</option>
                                    <option value="gpt-4o-2024-05-13">gpt-4o-2024-05-13</option>
                                    <option value="gpt-4-turbo-preview">gpt-4-turbo-preview</option>
                                    <option value="gpt-4-turbo-2024-04-09">gpt-4-turbo-2024-04-09</option>
                                    <option value="gpt-4-1106-preview">gpt-4-1106-preview</option>
                                    <option value="gpt-4-0613">gpt-4-0613</option>
                                    <option value="gpt-4-0125-preview">gpt-4-0125-preview</option>
                                    <option value="gpt-3.5-turbo-16k">gpt-3.5-turbo-16k</option>
                                    <option value="gpt-3.5-turbo-1106">gpt-3.5-turbo-1106</option>
                                    <option value="gpt-3.5-turbo-0125">gpt-3.5-turbo-0125</option>
                                </select>
                            </div>

                            <!-- Control de Temperatura -->
                            <div class="form-group">
                                <label for="editTemperature" class="mr-2">Temperatura</label>
                                <input type="range" class="form-control-range" id="editTemperature" name="temperature"
                                    min="0" max="2" step="0.1" value="1"
                                    oninput="updateTemperatureValue(this.value)">
                                <span class="ml-2" id="temperatureValue">1.00</span>
                            </div>

                            <!-- Control de Top P -->
                            <div class="form-group">
                                <label for="editTopP" class="mr-2">Top P</label>
                                <input type="range" class="form-control-range" id="editTopP" name="top_p"
                                    min="0" max="1" step="0.1" value="1"
                                    oninput="updateTopPValue(this.value)">
                                <span class="ml-2" id="topPValue">1.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
