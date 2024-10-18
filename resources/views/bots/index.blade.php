@extends('adminlte::page')

@section('title', 'Bots')

@section('content')
    <div class="row">
        <div class="col-lg-12 my-3">
            <div class="d-flex justify-content-end">
                <a data-toggle="modal" data-target="#createBotModal" class="btn btn-primary btn-sm mb-2" title="Crear bot">
                    <i class="fa fa-plus-circle"></i>
                </a>
            </div>
            <div class="d-flex justify-content-end">
                <a data-toggle="modal" data-target="#createAsistenteModal" class="btn btn-primary btn-sm mb-2" title="Crear Asistente">
                    <i class="fa fa-plus-circle"></i>
                    agregar nuevo asistente
                </a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table id="botsTable" class="table table-striped tabladatatable dt-responsive" style="width:100%">
        <thead>
            <tr>
                <th>Bot</th>
                <th>Descripción</th>
                <th>OpenAI Key</th>
                <th>OpenAI Org</th>
                <th>OpenAI Assistant</th>
                <th>Aplicación Asociada</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($todosLosBots as $bot)
                <tr>
                    <td>{{ $bot->nombre }}</td>
                    <td>{{ $bot->descripcion }}</td>
                    <td>{{ $bot->openai_key }}</td>
                    <td>{{ $bot->openai_org }}</td>
                    <td>{{ $bot->openai_assistant }}</td>
                    <td>
                        @if ($bot->aplicaciones->isNotEmpty())
                            {{ $bot->aplicaciones->first()->nombre }}
                        @else
                            Sin aplicación
                        @endif
                    </td>
                    <td>
                        <a data-toggle="modal" data-target="#modal-edit-{{ $bot->id }}"
                            class="btn btn-success btn-sm mb-2" title="Editar">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm mb-2 deleteBot" data-botid="{{ $bot->id }}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                {{-- Modal de edición --}}
                @include('bots.modals.edit-modal', ['bot' => $bot])
            @endforeach
        </tbody>
    </table>

    </div>
    @include('bots.modals.create-modal')
    @include('bots.modals.create-modal-asistente')
@endsection
@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.css">

@stop

@section('js')
    <script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        var table = $('#botsTable').DataTable({
            responsive: true
        });
    </script>
    <script>
        $(document).ready(function() {
            $('form[id^="editForm-"]').on('submit', function(e) {
                e.preventDefault();
                var botId = this.id.split('-')[1];
                var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "bots/" + botId,
                    data: formData,
                    success: function(response) {
                        if (response.data) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Actualizado!',
                                text: 'Bot actualizado con éxito',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo actualizar el bot.',
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error al actualizar el bot',
                        });
                    }
                });
            });
        });
        
        // Eliminar bot
        $('#botsTable').on('click', '.deleteBot', function() {
    var botId = $(this).data('botid');
    var row = $(this).parents('tr');
    
    console.log("Intentando eliminar el bot con ID: " + botId); // Añade esta línea para verificar el ID en consola

    Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar!',
        cancelButtonText: 'No, cancelar!',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'bots/' + botId,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content') // Verifica que este token se esté enviando correctamente
                },
                success: function(result) {
                    row.remove();
                    console.log("El bot ha sido eliminado con éxito"); // Verifica en consola si llega aquí
                    Swal.fire(
                        'Eliminado!',
                        'El bot ha sido eliminado.',
                        'success'
                    );
                },
                error: function(request, status, error) {
                    console.log("Error al intentar eliminar el bot", error); // Verifica el error
                    Swal.fire(
                        'Error!',
                        'No se pudo eliminar el bot.',
                        'error'
                    );
                }
            });
        }
    });
});

        
        // crear bot
        $('#createForm').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                type: "POST",
                url: "{{ route('bots.store') }}",
                data: formData,
                success: function(response) {
                    if (response.data) {

                        Swal.fire({
                            icon: 'success',
                            title: '¡Creado!',
                            text: 'bot creada con éxito',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Recarga la página para reflejar los cambios
                                location.reload();
                            }
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo obtener la información de la bot.',
                        });
                    }
                },
                error: function(error) {
                    // Captura el mensaje de error devuelto por el servidor
                    var errorMessage = error.responseJSON ? error.responseJSON.error :
                        'Error al crear la bot';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage, // Muestra el mensaje detallado
                    });
                }
            });
        });

        


        function getAssistantInfo(botId) {
    console.log("Bot ID:", botId); // Verifica el botId

    $.ajax({
        type: "GET",
        url: "bots/" + botId + "/assistant",
        success: function(response) {
            console.log("Response completa:", response); // Ver toda la respuesta

            if (response.data) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'Información del asistente recuperada con éxito',
                    confirmButtonText: 'OK'
                });

                // Insertar los datos en los campos de la tabla
                $('#assistant-id').text(response.data.file_id || 'N/A');
                $('#assistant-obj').text(response.data.object || 'N/A');
                $('#assistant-bytes').text(response.data.bytes || 'N/A');
                $('#assistant-createdAt').text(response.data.createdAt || 'N/A');
                $('#assistant-filename').text(response.data.filename || 'N/A');
                $('#assistant-purpose').text(response.data.purpose || 'N/A');
                $('#assistant-status').text(response.data.status || 'N/A');
                $('#assistant-status-details').text(response.data.status_details || 'N/A');

                // Abrir el modal o mostrar los datos
                $('#infoAssistantModal-' + botId).modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener la información del asistente.',
                });
            }
        },
        error: function(error) {
            var errorMessage = error.responseJSON ? error.responseJSON.error : 'Error al recuperar la información del asistente';
            console.log("Error:", errorMessage); // Log del error

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage,
            });
        }
    });
}








        // crear asistente
        $('#createFormAsistente').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: "{{ route('bots.store.asistente') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.data) {

                        Swal.fire({
                            icon: 'success',
                            title: '¡Creado!',
                            text: 'bot creada con éxito',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Recarga la página para reflejar los cambios
                                location.reload();
                            }
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo obtener la información de la bot.',
                        });
                    }
                },
                error: function(error) {
                    // Captura el mensaje de error devuelto por el servidor
                    var errorMessage = error.responseJSON ? error.responseJSON.error :
                        'Error al crear la bot';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage, // Muestra el mensaje detallado
                    });
                }
            });
        });
    </script>

@stop
