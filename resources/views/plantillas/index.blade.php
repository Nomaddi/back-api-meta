@extends('adminlte::page')

@section('plugins.Sweetalert2', true)

@section('title', 'Plantillas')

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <form id="createSend">
        <div class="card mt-4">
            <div class="card-header text-white bg-secondary mb-3">
                Envios masivos WhatsApp
            </div>
            <div class="card-body">
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

                <div>
                    <label for="templatesSelect">Seleccione una plantilla disponible</label>
                    <select id="templatesSelect" class="form-select form-control mb-3">
                        <option value="">Selecciona una plantilla</option>
                        <!-- Las opciones se cargarán aquí dinámicamente -->
                    </select>
                </div>
                <div>
                    <label for="selectTag">Seleccione un grupo para enviar</label>
                    <div>
                        <select id="etiqueta" name="etiqueta[]" class="form-select form-control mb-3" multiple>
                            <option value="">Selecciona una Etiqueta</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}"
                                    data-numeros="{{ implode("\n", $tag->contactos->pluck('telefono')->toArray()) }}">
                                    {{ $tag->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="exampleFormControlTextarea1">Lista de contactos</label>
                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                </div>

                <div id="templateDetails">
                    <!-- Los detalles de la plantilla se inyectarán aquí -->
                </div>
                <button type="submit" class="btn btn-primary">Enviar mensajes</button>

            </div>
        </div>

    </form>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        function findPlaceholders(text) {
            const regexp = /{{ '{' }}\d+{{ '}' }}/g;
            const matches = [];
            let match;
            while ((match = regexp.exec(text)) !== null) {
                matches.push({
                    text: match[0],
                    value: ''
                });
            }
            return matches;
        };
    </script>
    <script>
        $(document).ready(function() {
            $('#etiqueta').change(function() {
                updateContactList();
            });

            function updateContactList() {
                var selectedTag = $('#etiqueta option:selected');
                var numeros = selectedTag.map(function() {
                    return $(this).data('numeros');
                }).get().join('\n');

                $('#exampleFormControlTextarea1').val(numeros);
            }
        });
    </script>
    <script>
        var templatesData = []; // Almacenará la información de las plantillas
        var templateLanguage = null;
        var templateName = null;
        var templateType = null;

        $(document).ready(function() {
            $('#selectPlantilla').change(function() {
                var selectedOption = $(this).find('option:selected');
                var idCBusiness = selectedOption.data('id_c_business'); //id_telefono
                var tokenApi = selectedOption.data('token_api');

                if (idCBusiness && tokenApi) {
                    $.ajax({
                        url: 'message-templates', // Cambia esto por la ruta real a tu controlador
                        type: 'GET',
                        data: {
                            id_c_business: idCBusiness,
                            token_api: tokenApi
                        },
                        success: function(response) {
                            if (response.success) {
                                console.log(response.data);
                                // Actualiza la variable global con la respuesta
                                templatesData = response.data;

                                // Vacía el select antes de cargar nuevos datos para evitar duplicados
                                // $('#templatesSelect').empty();

                                // Itera sobre la respuesta y añade cada opción al select
                                response.data.forEach(function(item) {
                                    $('#templatesSelect').append(new Option(item.name,
                                        item.name
                                    )); // El texto y el valor de la opción son el nombre
                                });

                                // No olvides añadir aquí el código para manejar la selección inicial si es necesario
                            } else {
                                alert('No se pudieron cargar las plantillas.');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Ocurrió un error al cargar las plantillas.');
                        }
                    });
                }
            });

            $('#templatesSelect').change(function() {
                var selectedTemplateName = $(this).val();
                var selectedTemplate = templatesData.find(function(template) {
                    return template.name === selectedTemplateName;
                });
                // console.log(selectedTemplate);
                // console.log(selectedTemplate.language);
                if (selectedTemplate) {
                    templateLanguage = selectedTemplate.language; // Asigna el idioma a la variable
                    templateName = selectedTemplate.name; // Asigna el nombre a la variable
                } else {
                    templateLanguage = null; // Reinicia a null si no se encuentra la plantilla
                }
                // Construye el HTML para los detalles de la plantilla
                // Inicializa el HTML para los detalles de la plantilla
                var detailsHtml = '';

                // Itera sobre los componentes de la plantilla
                selectedTemplate.components.forEach(component => {
                    if (component.type === 'HEADER') {
                        if (component.format === 'DOCUMENT') {
                            templateType = 'DOCUMENT';
                            detailsHtml +=
                                `<div class="my-5"><h5 class="text-h5">Header</h5>
                                <div class="form-group">
                                    <label for="header">Link del documento</label>
                                    <input type="text" class="form-control" id="header" name="header" required>
                                </div>
                            </div>`;
                        } else {
                            templateType = 'TEXT';
                            detailsHtml +=
                                `<div class="my-5"><h5 class="text-h5">Header</h5><p>${component.text}</p></div>`;
                        }
                    } else if (component.type === 'BODY') {
                        var formattedText = component.text.replace(/\n/g, '<br>');
                        var placeholders = findPlaceholders(component.text);


                        // Genera HTML para los inputs de cada placeholder encontrado
                        var inputsHtml = placeholders.map(function(placeholder, index) {
                            return `<div class="form-group"><label for="${index}">${placeholder.text}</label><input type="text" class="form-control format" id="${index}" name="${index}" value="" /></div>`;
                        }).join('');

                        detailsHtml +=
                            `<div class="my-5"><h5 class="text-h5">Body</h5><p class="pre-wrap">${formattedText}</p>${inputsHtml}</div>`;
                    } else if (component.type === 'FOOTER') {
                        detailsHtml +=
                            `<div class="my-5"><h5 class="text-h5">Footer</h5><p class="pre-wrap">${component.text}</p></div>`;
                    } else if (component.type === 'BUTTONS') {
                        detailsHtml += '<div class="my-5"><h5 class="text-h5">Buttons</h5>';

                        // Verificar si hay elementos en el array "buttons"
                        if (component.buttons && component.buttons.length > 0) {
                            detailsHtml += '<ul>'; // Puedes usar una lista para mostrar los botones

                            // Iterar sobre cada botón en el array "buttons"
                            component.buttons.forEach(button => {
                                // Acceder a la URL de cada botón
                                const buttonUrl = button.url;

                                // Acceder a otros detalles del botón si es necesario (text, type, etc.)
                                const buttonText = button.text;

                                detailsHtml +=
                                    `<li>
                                        <p class="pre-wrap">${buttonText}</p>
                                        <p class="pre-wrap">${buttonUrl}</p>
                                        <div class="my-5"><h5 class="text-h5">Boton dinamico</h5>
                                        <div class="form-group">
                                            <label for="buttons">Completa la url del botton</label>
                                            <input type="text" class="form-control" id="buttons" name="buttons" required>
                                        </div>
                                    </li>`;
                            });

                            detailsHtml += '</ul>';
                        } else {
                            detailsHtml += '<p class="pre-wrap">No hay botones disponibles.</p>';
                        }

                        detailsHtml += '</div>';
                        // detailsHtml +=
                        //     `<div class="my-5"><h5 class="text-h5">Buttons</h5><p class="pre-wrap">${component.type}</p></div>`;
                    }
                });

                // Inyecta los detalles construidos en el contenedor
                $('#templateDetails').html(detailsHtml);
            });
        });
        $(document).ready(function() {
            $('#createSend').submit(function(e) {
                e.preventDefault(); // Prevenir la recarga de la página

                // Preparar los datos de los placeholders como un array
                var body_placeholders = [];
                $('#templateDetails .format').each(function() {
                    body_placeholders.push($(this).val());
                });

                // Inicializar variables en null
                var header_type = null;
                var header_url = null;
                var buttons_url = null;
                var id_c_business = null;
                var id_c_business2 = null;
                var phone_id2 = null;
                var phone_id = null;
                var recipients = null;
                var template_language = null;
                var template_name = null;
                var token_api = null;

                // Luego, asignar valores a las variables
                header_type = templateType; // Ahora asignas el valor deseado
                header_url = $('#header').val() ? $('#header').val() :
                    null; // Ahora asignas el valor deseado
                buttons_url = $('#buttons').val() ? $('#buttons').val() :
                    null; // Ahora asignas el valor deseado
                id_c_business2 = $('#selectPlantilla option:selected').data('id_c_business');
                id_c_business = id_c_business2.toString();
                phone_id2 = $('#selectPlantilla option:selected').data(
                    'id_telefono'); // Ahora asignas el valor deseado, si es dinámico, ajusta
                phone_id = phone_id2.toString();
                recipients = $('#exampleFormControlTextarea1')
                    .val(); // Ahora asignas el valor deseado, si es dinámico, ajusta
                template_language = templateLanguage; // Ahora asignas el valor deseado
                template_name = templateName; // Ahora asignas el valor deseado
                token_api = $('#selectPlantilla option:selected').data('token_api');

                // Organizar la información en un objeto
                var dataToSend = {
                    body_placeholders: body_placeholders,
                    header_type: header_type,
                    header_url: header_url,
                    buttons_url: buttons_url,
                    id_c_business: id_c_business,
                    phone_id: phone_id,
                    recipients: recipients,
                    template_language: template_language,
                    template_name: template_name,
                    token_api: token_api
                };
                // Mostrar el mensaje de carga con SweetAlert
                Swal.fire({
                    title: 'Cargando...',
                    allowOutsideClick: false,
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                });
                $.ajax({
                    type: "POST",
                    url: "send-message-templates",
                    contentType: "application/json",
                    data: JSON.stringify(dataToSend),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    async: true,
                    success: function(response) {
                        // Ocultar el mensaje de carga
                        Swal.close();

                        //limpiar formularo
                        $('#createSend').trigger("reset");

                        // Mostrar SweetAlert con la respuesta
                        Swal.fire('Envío correcto', response.message, 'success');
                    },
                    error: function(error) {
                        // Ocultar el mensaje de carga
                        Swal.close();

                        // Mostrar SweetAlert con el mensaje de error
                        Swal.fire('Error al enviar', 'Ha ocurrido un error durante el envío.',
                            'error');
                    }
                });
            });
        });
    </script>
@stop
