@extends('adminlte::page')

@section('title', 'Chats')

@section('content')
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <ul>
                    <li>{{ $error }}</li>
                </ul>
            @endforeach
        </div>
    @endif
    <!-- Modal -->
    <div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document"> <!-- Asegura que el modal se centre -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Seleciona una aplicación para ver sus chats</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span> <!-- Botón de cierre reintroducido -->
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Contenido del modal con dos selects -->
                    <div>
                        <label for="selectPlantilla">Seleccione un numero disponible</label>
                        <select id="selectPlantilla" class="form-select form-control mb-3">
                            <option value="">Selecciona un Número</option>
                            @foreach ($numeros as $numero)
                                <option value="{{ $numero->id }}" data-id_telefono="{{ $numero->id_telefono }}"
                                    data-id_c_business="{{ $numero->aplicacion->id_c_business }}"
                                    data-token_api="{{ $numero->aplicacion->token_api }}">
                                    {{ $numero->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container-fluid" id="main-container">
        <div class="row h-100">
            <div class="col-12 col-sm-5 col-md-4 d-flex flex-column" id="chat-list-area" style="position:relative;">

                <!-- Navbar -->
                <div class="row d-flex flex-row align-items-center p-2" id="navbar">
                    <img src="{{ asset('images/user.jpg') }}" alt="Profile Photo" class="img-fluid rounded-circle mr-2"
                        style="height:50px; cursor:pointer;" onclick="showProfileSettings()" id="display-pic">
                    <div class="text-white font-weight-bold" id="username">Contratacion Local</p>
                    </div>
                    <div class="nav-item dropdown ml-auto">
                        <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v text-white"></i></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#">New Group</a>
                            <a class="dropdown-item" href="#">Archived</a>
                            <a class="dropdown-item" href="#">Starred</a>
                            <a class="dropdown-item" href="#">Settings</a>
                            <a class="dropdown-item" href="#">Log Out</a>
                        </div>
                    </div>
                </div>

                <!-- Chat List -->
                <div class="row" id="chat-list" style="overflow:auto; max-height: 515px;"></div>

                <!-- Profile Settings -->
                <div class="d-flex flex-column w-100 h-100" id="profile-settings">
                    <div class="row d-flex flex-row align-items-center p-2 m-0"
                        style="background:#009688; min-height:65px;">
                        <i class="fas fa-arrow-left p-2 mx-3 my-1 text-white" style="font-size: 1.5rem; cursor: pointer;"
                            onclick="hideProfileSettings()"></i>
                        <div class="text-white font-weight-bold">Profile</div>
                    </div>
                    <div class="d-flex flex-column" style="overflow:auto;">
                        <img alt="Profile Photo" class="img-fluid rounded-circle my-5 justify-self-center mx-auto"
                            id="profile-pic">
                        <input type="file" id="profile-pic-input" class="d-none">
                        <div class="bg-white px-3 py-2">
                            <div class="text-muted mb-2"><label for="input-name">Tu nombre</label></div>
                            <input type="text" name="name" id="input-name" class="w-100 border-0 py-2 profile-input">
                        </div>
                        <div class="text-muted p-3 small">
                            Esta informacion no se mostrará a los contactos de WhatsApp.
                        </div>
                        <div class="bg-white px-3 py-2">
                            <div class="text-muted mb-2"><label for="input-about">Descripción</label></div>
                            <input type="text" name="name" id="input-about" value=""
                                class="w-100 border-0 py-2 profile-input">
                        </div>
                    </div>

                </div>
            </div>

            <!-- Message Area -->
            <div class="d-none d-sm-flex flex-column col-12 col-sm-7 col-md-8 p-0 h-100" id="message-area">
                <div class="w-100 h-100 overlay"></div>

                <!-- Navbar -->
                <div class="row d-flex flex-row align-items-center p-2 m-0 w-100" id="navbar">
                    <div class="d-block d-sm-none">
                        <i class="fas fa-arrow-left p-2 mr-2 text-white" style="font-size: 1.5rem; cursor: pointer;"
                            onclick="showChatList()"></i>
                    </div>
                    <a href="#"><img src="{{ asset('images/user.jpg') }}" alt="Profile Photo"
                            class="img-fluid rounded-circle mr-2" style="height:50px;" id="pic"></a>
                    <div class="d-flex flex-column">
                        <div class="text-white font-weight-bold" id="name"></div>
                        <div class="text-white small" id="details"></div>
                    </div>
                    <div class="d-flex flex-row align-items-center ml-auto">
                        <a href="#"><i class="fas fa-search mx-3 text-white d-none d-md-block"></i></a>
                        <a href="#"><i class="fas fa-paperclip mx-3 text-white d-none d-md-block"></i></a>
                        <a href="#"><i class="fas fa-ellipsis-v mr-2 mx-sm-3 text-white"></i></a>
                    </div>
                </div>

                <!-- Messages -->
                <div class="d-flex flex-column" id="messages"></div>

                <!-- Input -->
                <div class="d-none justify-self-end align-items-center flex-row" id="input-area">
                    <a href="#"><i class="far fa-smile text-muted px-3" style="font-size:1.5rem;"></i></a>
                    <input type="text" name="message" id="input" placeholder="Escribe un mensaje"
                        class="flex-grow-1 border-0 px-3 py-2 my-3 rounded shadow-sm">
                    <i class="fas fa-paper-plane text-muted px-3" style="cursor:pointer;" onclick="sendMessage()"></i>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.css">
    <link rel="stylesheet" href="{{ asset('css/chat.css') }}">
@stop

@section('js')
    <script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
    <script src="{{ asset('js/date-utils.js') }}"></script>

    {{-- pusher --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        var PUSHERGLOBAL = {
            pusherId: null,
        };
        var DOMNEWMESSAGE = {
            messages: document.getElementById("messages")
        };
        document.addEventListener("DOMContentLoaded", function() {

            // Pusher.logToConsole = true;

            var pusher = new Pusher('52c212ce563c5534e98c', { // Usa tu clave real aquí
                cluster: 'us2' // Usa tu cluster real aquí
            });

            var channel = pusher.subscribe('webhooks');
            channel.bind('App\\Events\\Webhook', function(payload) {
                // console.log(data);
                const message = payload.message;
                const changed = payload.change;

                // Asume que `selectedChat` y `messages` son gestionados de alguna manera (p.ej., variables globales, almacenados en localStorage, etc.)
                if (PUSHERGLOBAL.pusherId === message.wa_id) {
                    if (changed === false) {
                        appendMessage(message);
                        scrollToBottom();
                    } else {
                        let iconHTML = getStatusIcon(message.status);
                        updateMessageStatus(message.id, iconHTML);
                    }
                }
            });
        });

        function appendMessage(message) {

            fechaFormateada = formatISODateToCustomString(message.created_at);

            DOMNEWMESSAGE.messages.innerHTML += `
                    <div data-id="${message.id}" class="align-self-${message.outgoing ? "end self" : "start"} p-1 my-1 mx-3 rounded bg-white shadow-sm message-item">
                        <div class="options">
                            <a href="#"><i class="fas fa-angle-down text-muted px-2"></i></a>
                        </div>
                        <div class="message-content">
                            <div class="body m-1">${message.body}</div>
                            <div class="time ml-auto small text-muted" style="text-align: right;">${fechaFormateada}</div>
                        </div>
                    </div>
                `;

        }

        function scrollToBottom() {
            // Asegurarte de que el contenedor de mensajes se desplaza hacia abajo para mostrar el último mensaje
            DOMNEWMESSAGE.messages.scrollTo(0, DOMNEWMESSAGE.messages.scrollHeight);
        }

        function updateMessagesOnDOM(messages) {
            // Implementa esta función para actualizar todos los mensajes en el DOM
            // Nota: Este es un ejemplo simplificado. Necesitarás adaptarlo a tu estructura de mensajes específica.
            const chatContainer = document.querySelector("#chat-container");
            chatContainer.innerHTML = ''; // Limpia los mensajes actuales
            messages.forEach(appendMessage); // Re-añade los mensajes con la información actualizada
        }
    </script>
    <script>
        $(document).ready(function() {
            // Mostrar el modal al cargar la página
            $("#miModal").modal('show');
        });
        var userProfilePicUrl = "{{ asset('images/user.jpg') }}";
        const PERFIL = {
            profileSettings: document.getElementById(
                "profile-settings"),
            profilePic: document.getElementById(
                "profile-pic"),
            inputName: document.getElementById(
                "input-name"),
        };
        var AppConfig = {
            idTelefono: null,
            idCBusiness: null,
            tokenApi: null
        };

        let showProfileSettings = () => {
            PERFIL.profileSettings.style.left = 0;
            PERFIL.profilePic.src = userProfilePicUrl;
            PERFIL.inputName.value = 'Contratacion Local';
        };

        let hideProfileSettings = () => {
            PERFIL.profileSettings.style.left = "-110%";
            // DOM.username.innerHTML = user.name;
        };
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var input = document.getElementById("input"); // Obtiene el input por su ID

            input.addEventListener("keypress", function(event) {
                if (event.key === "Enter") { // Comprueba si la tecla presionada es Enter
                    event.preventDefault(); // Previene el comportamiento predeterminado de la tecla Enter
                    sendMessage(); // Llama a la función sendMessage
                }
            });
        });
    </script>
    <script>
        function sendMessage() {
            // Supongamos que tu URL es algo como "/get-chats" y esperas un parámetro "index"
            var idTelefono = AppConfig.idTelefono; //id_telefono
            var idCBusiness = AppConfig.idCBusiness; //id_telefono
            var tokenApi = AppConfig.tokenApi;

            // Obtén el valor del input
            var nameText = document.getElementById('name');
            var waId = nameText.innerText;
            var message = document.getElementById('input').value;
            if (message) {
                $.ajax({
                    url: 'messages/',
                    type: 'POST', // O 'POST' según tu implementación en el servidor
                    data: {
                        wa_id: waId,
                        id_phone: idTelefono,
                        token_api: tokenApi,
                        body: message
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Asumiendo que ya tienes una referencia al contenedor de mensajes
                            var DOM = {
                                messages: document.getElementById("messages")
                            };

                            let iconHTML = getStatusIcon(response.data.status);

                            let fechaM = formatISODateToCustomString(response.data.created_at)

                            // Agregar el nuevo mensaje al DOM
                            DOM.messages.innerHTML += `
                                <div data-id="${response.data.id}" class="align-self-${response.data.outgoing ? "end self" : "start"} p-1 my-1 mx-3 rounded bg-white shadow-sm message-item">
                                    <div class="options">
                                        <a href="#"><i class="fas fa-angle-down text-muted px-2"></i></a>
                                    </div>
                                    <div class="message-content">
                                        <div class="body m-1">${response.data.body}</div>
                                        <div class="hora time ml-auto small text-muted" style="text-align: right;">${fechaM}${(response.data.outgoing === 1) ? iconHTML : ""}</div>
                                        ${(response.dataoutgoing === 1) ? iconHTML : ""}
                                    </div>
                                </div>
                            `;
                            // Asegurarte de que el contenedor de mensajes se desplaza hacia abajo para mostrar el último mensaje
                            DOM.messages.scrollTo(0, DOM.messages.scrollHeight);

                            // Limpiar el input después de enviar el mensaje
                            document.getElementById('input').value = '';
                        } else {
                            // Manejar el caso en que la respuesta no es exitosa
                            console.log('No fue posible obtener los datos del mensaje.');
                        }
                    },

                    error: function(xhr, status, error) {
                        // Manejar posibles errores
                        console.error("No se pudo enviar el mensaje:", error);
                    }
                });
            }
        }
        $(document).ready(function() {
            $('#selectPlantilla').change(function() {
                var selectedOption = $(this).find('option:selected');
                var idTelefono = selectedOption.data('id_telefono'); //id_telefono

                var idCBusiness = selectedOption.data('id_c_business'); //id_telefono
                var tokenApi = selectedOption.data('token_api');

                // Almacenar en el objeto global
                AppConfig.idTelefono = selectedOption.data('id_telefono');
                AppConfig.idCBusiness = selectedOption.data('id_c_business');
                AppConfig.tokenApi = selectedOption.data('token_api');


                if (idTelefono) {
                    Swal.fire({
                        title: 'Cargando chats...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        onBeforeOpen: () => {
                            Swal.showLoading();
                        },
                    });
                    $.ajax({
                        url: 'messages', // Cambia esto por la ruta real a tu controlador
                        type: 'GET',
                        data: {
                            id_phone: idTelefono
                        },
                        success: function(response) {
                            Swal.close(); // Cierra el SweetAlert de carga
                            if (response.success) {
                                $("#miModal").modal('hide');
                                // No olvides añadir aquí el código para manejar la selección inicial si es necesario
                                $.each(response.data, function(index, elem) {
                                    // Decidir el ícono según el estado del mensaje

                                    let iconHTML = getStatusIcon(elem.status);


                                    // Construir el HTML para cada elemento del chat, incluyendo un data-id
                                    var chatItemHtml = `<div class="chat-list-item d-flex flex-row w-100 p-2 border-bottom" data-id="${elem.id}" onclick="generateMessageArea2('${elem.wa_id}', '${elem.phone_id}')">
                                                            <img src="{{ asset('images/user.jpg') }}" alt="Profile Photo" class="img-fluid rounded-circle mr-2" style="height:50px;">
                                                            <div class="w-50">
                                                                <div class="name">${elem.wa_id}</div>
                                                                <div class="small last-message">${(elem.outgoing === 1) ? iconHTML : ""}${elem.body}</div>
                                                            </div>
                                                            <div class="flex-grow-1 text-right">
                                                                <div class="small time">${mDate(elem.created_at).chatListFormat()}</div>
                                                            </div>
                                                        </div>`;

                                    // Agregar el HTML al div con ID 'chat-list'
                                    $("#chat-list").append(chatItemHtml);
                                });

                            } else {
                                Swal.fire('Error', 'No se pudieron cargar los chats.',
                                    'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.close();
                            Swal.fire('Error', 'Ocurrió un error al cargar las aplicaciones: ' +
                                error, 'error');
                        }
                    });
                }


            });
        });
    </script>
    <script>
        function generateMessageArea2(waId, phoneId, nextPageUrl = null) {
            // Supongamos que tu URL es algo como "/get-chats" y esperas un parámetro "index"
            if (waId) {
                Swal.fire({
                    title: 'Cargando mensajes...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                });
                // Si nextPageUrl es null, usamos la URL inicial para cargar los mensajes más recientes.
                // Si no, usamos nextPageUrl para cargar más mensajes.
                const url = nextPageUrl || 'messages/' + waId;
                $.ajax({
                    url: url,
                    type: 'GET', // O 'POST' según tu implementación en el servidor
                    data: {
                        id_phone: phoneId
                    },
                    success: function(response) {

                        Swal.close(); // Cierra el SweetAlert de carga
                        // console.log(response.data);

                        if (response.success && Object.keys(response.data).length > 0) {
                            const DOM = {
                                chatListArea: document.getElementById("chat-list-area"),
                                messageArea: document.getElementById("message-area"),
                                inputArea: document.getElementById("input-area"),
                                chatList: document.getElementById("chat-list"),
                                messages: document.getElementById("messages"),
                                chatListItem: document.getElementsByClassName("chat-list-item"),
                                messageAreaName: document.getElementById("name", this.messageArea),
                                messageAreaPic: document.getElementById("pic"),
                                messageAreaNavbar: document.getElementById("navbar", this.messageArea),
                                messageAreaDetails: document.getElementById("details"),
                                messageAreaOverlay: document.getElementsByClassName("overlay", this
                                    .messageArea)[0],
                                messageInput: document.getElementById("input"),
                                profilePic: document.getElementById("profile-pic"),
                                profilePicInput: document.getElementById("profile-pic-input"),
                                username: document.getElementById("username"),
                                displayPic: document.getElementById("display-pic"),
                            };
                            // Limpia el área de mensajes antes de añadir los nuevos mensajes
                            // DOM.messages.innerHTML = '';
                            if (!nextPageUrl) {
                                DOM.messages.innerHTML = '';
                            }
                            let messagesHTML = '';
                            // Revisa si ya existe un botón de "Cargar más mensajes" y lo remueve
                            let existingLoadMoreBtn = document.getElementById('loadMoreMessagesBtn');
                            if (existingLoadMoreBtn) {
                                existingLoadMoreBtn.parentNode.removeChild(existingLoadMoreBtn);
                            }
                            // Itera sobre cada grupo de mensajes por fecha.
                            $.each(response.data, function(date, messages) {
                                // Agrega un divisor para cada fecha
                                let dateDivider = `
                                                        <div class="mx-auto my-2 bg-primary text-white small py-1 px-2 rounded">
                                                            ${date}
                                                        </div>

                                                    `;
                                let groupHTML = dateDivider;

                                // Convierte el objeto de mensajes en un arreglo de mensajes.
                                let messagesGroupArray = Object.values(messages);

                                // Ordena los mensajes de más antiguo a más reciente.
                                messagesGroupArray.sort(function(a, b) {
                                    return new Date(a.created_at) - new Date(b.created_at);
                                });

                                $.each(messagesGroupArray, function(index, elem) {
                                    mClassList(DOM.inputArea).contains("d-none", (elem) => elem
                                        .remove(
                                            "d-none").add("d-flex"));

                                    mClassList(DOM.messageAreaOverlay).add("d-none");
                                    DOM.messageAreaName.innerHTML = elem.wa_id;
                                    PUSHERGLOBAL.pusherId = elem.wa_id;

                                    DOM.messageAreaDetails.innerHTML = "Ultimo mensaje: " + elem
                                        .created_at;


                                    // Ajustes de visualización responsiva.
                                    if (window.innerWidth <= 575) {
                                        mClassList(DOM.chatListArea).remove("d-flex").add(
                                            "d-none");
                                        mClassList(DOM.messageArea).remove("d-none").add(
                                            "d-flex");
                                        // Asume que esta variable controla el estado del layout. Debería ser manejada adecuadamente en tu lógica.
                                        areaSwapped = true;
                                    }

                                    let iconHTML = getStatusIcon(elem.status);

                                    // Si necesitas actualizar la UI con datos específicos de 'chat', asegúrate de hacerlo correctamente aquí.
                                    // Por ejemplo, si quieres actualizar el nombre y la imagen en el área de mensajes basado en 'chat'.
                                    let messageHTML = `
                                    <div data-id="${elem.id}" class="align-self-${elem.outgoing === 1 ? "end self" : "start"} p-1 my-1 mx-3 rounded bg-white shadow-sm message-item">
                                        <div class="options">
                                            <a href="#"><i class="fas fa-angle-down text-muted px-2"></i></a>
                                        </div>

                                        <div class="message-content">
                                        <div class="message sent">${elem.body}
                                            <span class="metadata">
                                                <span class="time">${mDate(elem.created_at).getTime()}</span>
                                                ${(elem.outgoing === 1) ? iconHTML : ""}
                                            </span>
                                        </div>
                                    </div>
                                    </div>
                                    `;
                                    groupHTML += messageHTML; // Agrega el mensaje al grupo.
                                    // DOM.messages.scrollTo(0, DOM.messages.scrollHeight);
                                });
                                messagesHTML = groupHTML + messagesHTML;
                            });
                            // Insertamos todos los mensajes al inicio del área de mensajes.
                            DOM.messages.innerHTML = messagesHTML + DOM.messages.innerHTML;
                            if (!nextPageUrl) {
                                DOM.messages.scrollTo(0, DOM.messages.scrollHeight);
                            }

                            // Insertamos el botón de "Cargar más mensajes" si existe nextPageUrl en la respuesta.
                            if (response.nextPageUrl && !document.getElementById('loadMoreMessagesBtn')) {
                                const loadMoreBtn = document.createElement('button');
                                loadMoreBtn.id = 'loadMoreMessagesBtn';
                                loadMoreBtn.textContent = 'Cargar más mensajes';
                                loadMoreBtn.onclick = function() {
                                    generateMessageArea2(waId, phoneId, response.nextPageUrl);
                                };
                                DOM.messages.insertBefore(loadMoreBtn, DOM.messages.firstChild);
                            }

                            // DOM.messages.scrollTo(0, DOM.messages.scrollHeight);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Manejar posibles errores
                        console.error("Error al obtener los chats:", error);
                    }
                });
            }
        }

        let mClassList = (element) => {
            return {
                add: (className) => {
                    element.classList.add(className);
                    return mClassList(element);
                },
                remove: (className) => {
                    element.classList.remove(className);
                    return mClassList(element);
                },
                contains: (className, callback) => {
                    if (element.classList.contains(className))
                        callback(mClassList(element));
                }
            };
        };
    </script>
    <script>
        function formatISODateToCustomString(fechaISO) {
            var fecha = new Date(fechaISO);

            // Construir la fecha manualmente
            var year = fecha.getFullYear();
            // getMonth() devuelve un valor entre 0 (enero) y 11 (diciembre), así que se suma 1.
            // padStart(2, '0') asegura que el mes y día sean de dos dígitos.
            var month = (fecha.getMonth() + 1).toString().padStart(2, '0');
            var day = fecha.getDate().toString().padStart(2, '0');

            // Horas, minutos y segundos
            var hours = fecha.getHours().toString().padStart(2, '0');
            var minutes = fecha.getMinutes().toString().padStart(2, '0');
            var seconds = fecha.getSeconds().toString().padStart(2, '0');

            // Formato final
            var fechaFormateada = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
            return fechaFormateada;
        }
    </script>
    <script>
        function getStatusIcon(status) {
            let statusIcon;

            switch (status) {
                case 'read':
                    statusIcon = '<span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"></path></svg></span>';
                    break;
                case 'delivered':
                    statusIcon = '<span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#7D8489"></path></svg></span>';
                    break;
                case 'sent':
                    statusIcon = '<span class="tick"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-check-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#7D8489"></path></svg></span>';
                    break;
                default:
                    statusIcon = "";
                    break;
            }

            return statusIcon;
        }
    </script>
    <script>
        function updateMessageStatus(messageId, status) {
            // Encuentra el elemento específico por su data-id.
            const messageElement = document.querySelector(`div[data-id="${messageId}"]`);
            if (messageElement) {
                // Encuentra el contenedor de tiempo dentro de ese elemento.
                const timeContainer = messageElement.querySelector('.time');
                if (timeContainer) {
                    // Actualiza el HTML del contenedor de tiempo con el nuevo ícono.
                    // Asumiendo que quieres mantener la fecha/hora existente y solo cambiar el ícono.
                    // Separa el contenido actual por el ícono para mantener el texto existente.
                    const parts = timeContainer.innerHTML.split('<i');
                    const newText = parts[0].trim(); // Elimina espacios en blanco innecesarios.
                    timeContainer.innerHTML = `${newText} ${status}`;
                }
            }
        }
    </script>
@stop