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
        console.error('Bot ID no encontrado en la URL del script');
        return;
    }

    // Crear el botón flotante de chat
    var chatButton = document.createElement('button');
    chatButton.innerHTML = `<span style="margin-right: 8px;">💬</span> Chat con ${botNombre || 'Asistente'}`;
    Object.assign(chatButton.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        backgroundColor: '#6200ea',  // Color morado
        color: '#fff',
        border: 'none',
        borderRadius: '50px',
        padding: '12px 25px',
        boxShadow: '0px 4px 8px rgba(0,0,0,0.2)',
        fontSize: '16px',  // Tamaño de fuente ajustado
        cursor: 'pointer',
        zIndex: '1000',
        transition: 'transform 0.3s ease'
    });

    // Crear el botón flotante de chat
    // var chatButton = document.createElement('button');
    // chatButton.innerHTML = `Chat con ${botNombre || 'Asistente'}`;
    // chatButton.style.position = 'fixed';
    // chatButton.style.bottom = '20px';
    // chatButton.style.right = '20px';
    // chatButton.style.backgroundColor = '#007bff';
    // chatButton.style.color = '#fff';
    // chatButton.style.border = 'none';
    // chatButton.style.borderRadius = '50px';
    // chatButton.style.padding = '10px 20px';
    // chatButton.style.boxShadow = '0px 2px 5px rgba(0,0,0,0.3)';
    // chatButton.style.cursor = 'pointer';
    // chatButton.style.zIndex = '1000';
    document.body.appendChild(chatButton);

    // Crear el contenedor del modal con estilos personalizados
    var chatModal = document.createElement('div');
    chatModal.innerHTML = `
        <div class="modal" id="embedded-chat-modal" style="position: fixed; bottom: 20px; right: 20px; width: 360px; height: 500px; background: #ffffff; border-radius: 12px; box-shadow: 0 6px 16px rgba(0,0,0,0.3); display: none; z-index: 1001;">
            <div class="modal-header" style="padding: 16px; background-color: #0052cc; color: #fff; border-top-left-radius: 12px; border-top-right-radius: 12px; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title" id="chatModalLabel" style="margin: 0; font-size: 18px;">Chat con ${botNombre || 'Asistente'}</h5>
                <button type="button" class="close" style="background: none; border: none; color: white; font-size: 20px;" onclick="document.getElementById('embedded-chat-modal').style.display='none'">
                    <svg width="24" height="24" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 15px; height: 360px; overflow-y: auto; background-color: #f7f9fc;" id="chat-box">
                <div class="chat-message bot-message" style="background: #e0e7ff; padding: 12px; margin-bottom: 12px; border-radius: 10px; max-width: 75%; font-size: 14px;">
                    <p style="margin: 0;">¡Hola! ¿Cómo puedo ayudarte hoy?</p>
                </div>
            </div>
            <div class="modal-footer" style="padding: 12px; background-color: #f1f1f1; display: flex; align-item: center;">
                <input type="text" id="user-input" name="user-input" class="form-control" placeholder="Escribe un mensaje..." style="flex-grow: 1; border-radius: 8px; padding: 10px; font-size: 14px; border: 1px solid #ccc; " />
                <button type="button" class="btn btn-primary" id="send-btn" style="margin-left: 8px; background-color: #0052cc; border: none; border-radius: 8px; padding: 10px 18px; color: #fff; font-size: 14px;">Enviar</button>
            </div>
        </div>
    `;

    document.body.appendChild(chatModal);

    // Mostrar el modal cuando se haga clic en el botón
    chatButton.addEventListener('click', function () {
        var modal = document.getElementById('embedded-chat-modal');
        if (modal) {
            modal.style.display = 'block';
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.style.transform = 'translateY(0)';
            }, 50);
        } else {
            console.error('No se pudo encontrar el modal en el DOM.');
        }
    });

    // Enviar mensaje del usuario
    var sendButton = document.getElementById('send-btn');
    if (sendButton) {
        sendButton.addEventListener('click', function () {
            var userInput = document.getElementById('user-input');
            if (userInput && userInput.value.trim()) {
                var chatBox = document.getElementById('chat-box');
                if (chatBox) {
                    var userMessageValue = userInput.value.trim();

                    // Añadir el mensaje del usuario al chat
                    var userMessage = document.createElement('div');
                    userMessage.classList.add('chat-message', 'user-message');
                    userMessage.innerHTML = '<p>' + userMessageValue + '</p>';
                    userMessage.style.background = '#0052cc';
                    userMessage.style.color = 'white';
                    userMessage.style.padding = '10px';
                    userMessage.style.marginBottom = '10px';
                    userMessage.style.borderRadius = '10px';
                    userMessage.style.textAlign = 'right';
                    userMessage.style.maxWidth = '75%';
                    userMessage.style.boxShadow = '0px 2px 5px rgba(0, 0, 0, 0.15)'; 
                    chatBox.appendChild(userMessage);
                    userInput.value = ''; // Limpiar input

                    var loadingIndicator = document.createElement('div');
                    loadingIndicator.classList.add('loading-indicator');
                    loadingIndicator.innerHTML = 'Escribiendo...';
                    loadingIndicator.style = 'text-align: center; color: #bbb; font-size: 12px; padding: 5px;';
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
                        sendButton.disabled = false;
                        if (data.answer) {
                            var botMessage = document.createElement('div');
                            botMessage.classList.add('chat-message', 'bot-message');
                            botMessage.innerHTML = '<p>' + data.answer + '</p>';
                            botMessage.style.background = '#e0e7ff';
                            botMessage.style.padding = '10px';
                            botMessage.style.marginBottom = '10px';
                            botMessage.style.borderRadius = '10px';
                            botMessage.style.maxWidth = '75%';
                            chatBox.appendChild(botMessage);
                            botMessage.style.boxShadow = '0px 2px 5px rgba(0, 0, 0, 0.15)';


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
    } else {
        console.error('No se pudo encontrar el botón de envío en el DOM.');
    }
});
