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

   // Añadir clase al botón de chat
    var chatButton = document.createElement('button');
    chatButton.classList.add('chat-button-style');
    chatButton.innerHTML = `Chat con ${botNombre || 'Asistente'}`;
    document.body.appendChild(chatButton);

    // Añadir clases al modal y sus componentes
    var chatModal = document.createElement('div');

    chatModal.classList.add('modal-style');
    chatModal.innerHTML = `
        <div class="modal-header modal-header-style">
            <h5 class="modal-title">Chat con ${botNombre || 'Asistente'}</h5>
            <button type="button" class="close" onclick="document.getElementById('embedded-chat-modal').style.display='none'">✖️</button>
        </div>
        <div class="modal-body modal-body-style" id="chat-box">
            <div class="chat-message bot-message"> 
                <p>¡Hola! ¿Cómo puedo ayudarte hoy?</p>
            </div>
        </div>
        <div class="modal-footer modal-footer-style">
            <input type="text" id="user-input" placeholder="Escribe un mensaje...">
            <button type="button" id="send-btn">Enviar</button>
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
                    userMessage.style.background = '#007bff';
                    userMessage.style.color = 'white';
                    userMessage.style.padding = '10px';
                    userMessage.style.marginBottom = '10px';
                    userMessage.style.borderRadius = '20px 20px 0px 20px';
                    userMessage.style.textAlign = 'right';
                    userMessage.style.maxWidth = '80%';
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
                            botMessage.innerHTML = '<p>' + data.answer + '</p>';
                            botMessage.style.background = '#e9ecef';
                            botMessage.style.padding = '10px';
                            botMessage.style.marginBottom = '10px';
                            botMessage.style.borderRadius = '20px 20px 20px 0px';
                            botMessage.style.maxWidth = '80%';
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
    } else {
        console.error('No se pudo encontrar el botón de envío en el DOM.');
    }
});