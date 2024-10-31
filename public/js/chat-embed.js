document.addEventListener('DOMContentLoaded', function () {
    function getBotDataFromScript() {
        var scripts = document.getElementsByTagName('script');
        for (var i = 0; i < scripts.length; i++) {
            var script = scripts[i];
            if (script.src && script.src.includes('chat-embed.js')) {
                var urlParams = new URLSearchParams(script.src.split('?')[1]);
                return {
                    botId: urlParams.get('botId'),
                    botNombre: urlParams.get('botNombre')
                };
            }
        }
        return { botId: null, botNombre: null };
    }

    var { botId, botNombre } = getBotDataFromScript();

    if (!botId) {
        console.warn('Bot ID no encontrado en la URL del script');
        return;
    }

    var chatButton = document.createElement('button');
    chatButton.id = 'chat-button';  // Asigna un id al botón
    chatButton.innerHTML = `<span style="margin-right: 8px;">💬</span> Chat con ${botNombre || 'Asistente'}`;

    chatButton.addEventListener('mouseenter', function () {
        chatButton.style.transform = 'scale(1.05)';
    });
    chatButton.addEventListener('mouseleave', function () {
        chatButton.style.transform = 'scale(1)';
    });

    
    document.body.appendChild(chatButton);

    // Crear el contenedor del modal con estilos personalizados
    var chatModal = document.createElement('div');
    chatModal.innerHTML = `
        <div class="modal" id="embedded-chat-modal"> 
            <div class="modal-header"> 
                <h5 class="modal-title" id="chatModalLabel">Chat con ${botNombre || 'Asistente'}</h5>
                <button type="button" class="close" onclick="document.getElementById('embedded-chat-modal').style.display='none'">
                    <svg width="24" height="24" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="modal-body" id="chat-box">
                <div class="chat-message bot-message"> 
                    <p>¡Hola! ¿Cómo puedo ayudarte hoy?</p>
                </div>
            </div>
            <div class="modal-footer">
                <input type="text" id="user-input" name="user-input" class="form-control" placeholder="Escribe un mensaje..." /> 
                <button type="button" class="btn btn-primary" id="send-btn">Enviar</button>
            </div>
        </div>
    `;

    document.body.appendChild(chatModal);

    // Mostrar el modal cuando se haga clic en el botón
    chatButton.addEventListener('click', function () {
        var modal = document.getElementById('embedded-chat-modal');
        if (modal) {
            modal.style.display = 'block';
            modal.classList.add('show'); // Añade la clase 'show' que contiene la animación en CSS
        }        
    });

    //funcion para cerrar el modal
    window.closeChatModal = function () {
        var modal = document.getElementById('embedded-chat-modal');
        if (modal) {
            modal.style.display = 'none';   // Oculta el modal
            modal.classList.remove('show'); // Elimina la clase 'show' para reiniciar el estado
        }
    };

    // Enviar mensaje del usuario
    var sendButton = document.getElementById('send-btn');
    var userInput = document.getElementById('user-input'); // Define userInput aquí

    if (sendButton && userInput) {
        // Evento para enviar al hacer click en el boton de envio
        sendButton.addEventListener('click', function () {
            if (userInput.value.trim()) {
                var chatBox = document.getElementById('chat-box');
                if (chatBox) {
                    var userMessageValue = userInput.value.trim();

                    // Añadir el mensaje del usuario al chat
                    var userMessage = document.createElement('div');


                    userMessage.classList.add('chat-message', 'user-message');
                    userMessage.innerHTML = '<p>' + userMessageValue + '</p>';
                    chatBox.appendChild(userMessage);
                    userInput.value = ''; // Limpiar input
                    userInput.focus(); // mantener el foco en el campo de entrada

                    // Crear el indicador de carga
                    var loadingIndicator = document.createElement('div');
                    loadingIndicator.classList.add('loading-indicator');
                    loadingIndicator.innerHTML = 'Escribiendo...';
                    chatBox.appendChild(loadingIndicator);

                    // Enviar mensaje al servidor
                    fetch('http://127.0.0.1:8000/admin/ask-bot-embedded', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            question: userMessageValue,
                            botId: botId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Eliminar el indicador de carga después de recibir la respuesta
                        chatBox.removeChild(loadingIndicator);

                        if (data.answer) {
                            var botMessage = document.createElement('div');
                            botMessage.classList.add('chat-message', 'bot-message');
                            botMessage.innerHTML = `<i class="fas fa-robot" style="margin-right: 8px;"></i><p>${data.answer}</p>`;
                            chatBox.appendChild(botMessage);
                            chatBox.scrollTop = chatBox.scrollHeight;


                            // Desplazar hacia abajo para mostrar el mensaje más reciente
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }
                    })
                    .catch(error => {
                        // Manejo de errores: eliminar el indicador de carga y loguear el error
                        chatBox.removeChild(loadingIndicator);
                        console.error('Error al enviar la solicitud:', error);
                    });
                } else {
                    console.error('No se pudo encontrar el chat box en el DOM.');
                }
            } else {
                console.error('Input de usuario no encontrado o está vacío.');
            }
        });

        // Evento para enviar al presionar la tecla Enter
        userInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();  // Prevenir salto de línea
                sendButton.click();
            }
        });

    } else {
        console.error('No se pudo encontrar el botón de envío en el DOM.');
    }
});
