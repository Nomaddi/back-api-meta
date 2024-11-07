document.addEventListener('DOMContentLoaded', function () {
    // Agregar una hoja de estilos externa al DOM
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    // local
    // link.href = 'http://127.0.0.1:8000/css/chat-embed.css'; // producción
    link.href = 'https://maddigo.com.co/css/chat-embed.css'; // producción
    document.head.appendChild(link);

    var faLink = document.createElement('link');
    faLink.rel = 'stylesheet';
    faLink.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css';
    document.head.appendChild(faLink);

    // Agregar el script externo de marked.js
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/marked/marked.min.js';
    document.head.appendChild(script);

    // Función para generar un UUID
    function generateUUID() {
        return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
        );
    }

    // Obtener o crear el UUID del usuario
    function getUserIdentifier() {
        let userId = localStorage.getItem('userIdentifier');
        if (!userId) {
            userId = generateUUID();
            localStorage.setItem('userIdentifier', userId);
        }
        return userId;
    }

    const userIdentifier = getUserIdentifier();

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
    chatButton.className = 'chat-embed-button';
    chatButton.innerHTML = `Chat con ${botNombre || 'Asistente'}`;
    document.body.appendChild(chatButton);

    // Crear el contenedor principal para el chat
    var chatEmbedContainer = document.createElement('div');
    chatEmbedContainer.className = 'chat-embed';
    chatEmbedContainer.innerHTML = `
        <div class="chat-embed-modal" id="embedded-chat-modal" style="display: none;">
            <div class="chat-embed-modal-header">
                <h5 class="chat-embed-modal-title" id="chatModalLabel">Chat con ${botNombre || 'Asistente'}</h5>
                <button type="button" class="chat-embed-close" onclick="document.getElementById('embedded-chat-modal').style.display='none'">&times;</button>
            </div>
            <div class="chat-embed-modal-body" id="chat-box">
                <div class="chat-embed-message bot-message">
                    <p>¡Hola! ¿Cómo puedo ayudarte hoy?</p>
                </div>
            </div>
            <div class="chat-embed-modal-footer">
                <input type="text" id="user-input" name="user-input" class="chat-embed-input" placeholder="Escribe un mensaje..." />
                <button type="button" class="chat-embed-send-btn" id="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(chatEmbedContainer);

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
                    userMessage.classList.add('chat-embed-message', 'user-message');
                    userMessage.innerHTML = '<i class="fas fa-user" style="margin-right: 8px;"></i>' + '<p>' + marked.parse(userMessageValue) + '</p>';
                    chatBox.appendChild(userMessage);
                    userInput.value = ''; // Limpiar input

                    // Enviar mensaje al servidor
                    // fetch('http://127.0.0.1:8000/admin/ask-bot-embedded', {
                    fetch('https://maddigo.com.co/admin/ask-bot-embedded', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            question: userMessageValue,
                            botId: botId,
                            userIdentifier: userIdentifier
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.answer) {
                                var botMessage = document.createElement('div');
                                botMessage.classList.add('chat-embed-message', 'bot-message');
                                botMessage.innerHTML = `
                                    <i class="fas fa-robot" style="margin-right: 8px;"></i>
                                    <div class="chat-embed-message-content">
                                        ${marked.parse(data.answer)}
                                    </div>
                                `;
                                chatBox.appendChild(botMessage);
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
