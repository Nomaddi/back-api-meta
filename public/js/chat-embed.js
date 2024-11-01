document.addEventListener('DOMContentLoaded', function () {
    // Agregar una hoja de estilos externa al DOM
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'http://127.0.0.1:8000/css/chat-embed.css'; // Cambia 'ruta/a/tu/archivo/styles.css' por la ruta de tu archivo CSS
    document.head.appendChild(link);

    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
    document.head.appendChild(link);

    // Agregar el script externo de marked.js
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/marked/marked.min.js';
    document.head.appendChild(script);

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
        console.error('Bot ID no encontrado en la URL del script');
        return;
    }

    // Crear el botón flotante de chat
    var chatButton = document.createElement('button');
    chatButton.innerHTML = `Chat con ${botNombre || 'Asistente'}`;
    chatButton.style.position = 'fixed';
    chatButton.style.bottom = '20px';
    chatButton.style.right = '20px';
    chatButton.style.backgroundColor = '#007bff';
    chatButton.style.color = '#fff';
    chatButton.style.border = 'none';
    chatButton.style.borderRadius = '50px';
    chatButton.style.padding = '10px 20px';
    chatButton.style.boxShadow = '0px 2px 5px rgba(0,0,0,0.3)';
    chatButton.style.cursor = 'pointer';
    chatButton.style.zIndex = '1000';
    document.body.appendChild(chatButton);

    // Crear el contenedor del modal con el nombre del bot
    var chatModal = document.createElement('div');
    chatModal.innerHTML = `
        <div class="modal" id="embedded-chat-modal" style="position: fixed; bottom: 20px; right: 20px; width: 350px; height: 454px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); display: none; z-index: 1001;">
            <div class="modal-header" style="padding: 10px; background-color: #007bff; color: #fff; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <h5 class="modal-title" id="chatModalLabel" style="margin: 0; font-size: 16px;">Chat con ${botNombre || 'Asistente'}</h5>
                <button type="button" class="close" style="background: none; border: none; color: white; font-size: 20px;" onclick="document.getElementById('embedded-chat-modal').style.display='none'">&times;</button>
            </div>
            <div class="modal-body" style="padding: 10px; height: 350px; overflow-y: auto; background-color: #f9f9f9;" id="chat-box">
                <div class="chat-message bot-message" style="background: #e9ecef; padding: 10px; margin-bottom: 10px; border-radius: 20px 20px 20px 0px; max-width: 100%; font-size: 14px;">
                    <p style="margin: 0;">¡Hola! ¿Cómo puedo ayudarte hoy?</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 10px; background-color: #f1f1f1; display: flex;">
                <input type="text" id="user-input" name="user-input" class="form-control" placeholder="Escribe un mensaje..." style="flex-grow: 1; border-radius: 20px; padding: 10px;" />
                <button type="button" class="btn btn-primary" id="send-btn" style="margin-left: 5px; background-color: #007bff; border: none; border-radius: 50px; padding: 10px 20px;">Enviar</button>
            </div>
        </div>
    `;
    document.body.appendChild(chatModal);

    // Mostrar el modal cuando se haga clic en el botón
    chatButton.addEventListener('click', function () {
        var modal = document.getElementById('embedded-chat-modal');
        if (modal) {
            modal.style.display = 'block';
        } else {
            console.error('No se pudo encontrar el modal en el DOM.');
        }
    });

    // Enviar mensaje del usuario
    var sendButton = document.getElementById('send-btn');
    var userInput = document.getElementById('user-input');
    if (sendButton) {
        sendButton.addEventListener('click', function () {
            if (userInput && userInput.value.trim()) {
                var chatBox = document.getElementById('chat-box');
                if (chatBox) {
                    var userMessageValue = userInput.value.trim();

                    // Añadir el mensaje del usuario al chat
                    var userMessage = document.createElement('div');
                    userMessage.classList.add('chat-message', 'user-message');
                    userMessage.innerHTML = '<i class="fas fa-user" style="margin-right: 8px;"></i>' + '<p>' + marked.parse(userMessageValue) + '</p>';
                    userMessage.style.background = '#007bff';
                    userMessage.style.color = 'white';
                    userMessage.style.padding = '10px';
                    userMessage.style.marginBottom = '10px';
                    userMessage.style.borderRadius = '20px 20px 0px 20px';
                    userMessage.style.textAlign = 'right';
                    userMessage.style.maxWidth = '100%';
                    chatBox.appendChild(userMessage);
                    userInput.value = ''; // Limpiar input

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
                            if (data.answer) {
                                var botMessage = document.createElement('div');
                                botMessage.classList.add('chat-message', 'bot-message');
                                botMessage.innerHTML = `
                                    <i class="fas fa-robot" style="margin-right: 8px;"></i>
                                    <div class="bot-message-content">
                                        ${marked.parse(data.answer)}
                                    </div>
                                `;
                                botMessage.style.background = '#e9ecef';
                                botMessage.style.padding = '10px';
                                botMessage.style.marginBottom = '10px';
                                botMessage.style.borderRadius = '20px 20px 20px 0px';
                                botMessage.style.maxWidth = '100%';
                                chatBox.appendChild(botMessage);

                                // Desplazar hacia abajo para mostrar el mensaje más reciente
                                chatBox.scrollTop = chatBox.scrollHeight;
                            }
                        })
                        .catch(error => {
                            console.error('Error al enviar la solicitud:', error);
                        });
                } else {
                    console.error('No se pudo encontrar el chat box en el DOM.');
                }
            } else {
                console.error('Input de usuario no encontrado o está vacío.');
            }
        });

        // Agregar el evento keypress para el envío con Enter
        userInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                sendButton.click();
            }
        });
    } else {
        console.error('No se pudo encontrar el botón de envío en el DOM.');
    }
});
