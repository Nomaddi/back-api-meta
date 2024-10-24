<div class="modal fade" id="modal-bot-{{ $bot->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chat con Asistente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="chat-box" id="chat-box">
                    <div class="chat-message bot-message">
                        <p>¡Hola! ¿Cómo puedo ayudarte hoy?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row w-100">
                    <div class="col-10">
                        <input type="text" id="user-input" class="form-control" placeholder="Escribe un mensaje..." />
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-primary w-100" id="send-btn">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
