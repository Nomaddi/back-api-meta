@extends('adminlte::page')

@section('title', 'Contactos')

@section('content')
    <div class="row">
        <div class="col-lg-12 my-3">
            <div>
                <a data-toggle="modal" data-target="#createContactoModal" class="btn btn-primary btn-sm mb-2" title="Crear">
                    <i class="fa fa-plus-circle"></i>
                </a>
                <a data-toggle="modal" data-target="#importAppModal" class="btn btn-success btn-sm mb-2" title="Importar">
                    <i class="fa fa-file-import"></i>
                </a>

            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table id="contactosTable" class="table table-striped table-bordered shadow-lg mt-4 display compact" style="width:100%">
        <thead class="bg-primary text-white">
            <tr>
                <th>No</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Etiqueta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center"></tbody>
    </table>

    {{-- @include('contactos.modals.show-modal')
    @include('contactos.modals.import-modal')
    @include('contactos.modals.edit-modal')
    @include('contactos.modals.delete-modal') --}}
    @include('contactos.modals.create-modal')
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
        $(document).ready(function() {
            $('#contactosTable').DataTable({
                processing: true,
                serverSide: true, // Aquí se habilita la paginación y búsqueda del lado del servidor
                ajax: '{{ route('contactos.getData') }}',
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'telefono'
                    },
                    {
                        data: 'tags'
                    },
                    {
                        data: 'actions'
                    },
                ]
            });
        });
    </script>
    <script>
        // Script para agregar validación personalizada usando JavaScript
        document.getElementById('telefono').addEventListener('input', function() {
            var telefonoInput = this;
            var validacion = /^\+57\d{10}$/;

            if (!validacion.test(telefonoInput.value)) {
                telefonoInput.setCustomValidity('El teléfono debe comenzar con +57 y tener 10 cifras');
            } else {
                telefonoInput.setCustomValidity('');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('form[id^="editForm-"]').on('submit', function(e) {
                e.preventDefault(); // Evitar la recarga de la página
                var appId = this.id.split('-')[1]; // Obtener el ID de la aplicación
                var formData = $(this).serialize(); // Serializar los datos del formulario

                $.ajax({
                    type: "POST",
                    url: "contactos/" + appId, // Ajusta esta URL según tu enrutamiento
                    data: formData,
                    success: function(response) {
                        // $('#modal-show-' + appId).modal('hide'); // Ocultar el modal
                        alert("Contacto actualizado con éxito"); // Mostrar mensaje de éxito
                        location
                            .reload(); // Opcional: recargar la página o actualizar la vista de alguna otra manera
                    },
                    error: function(error) {
                        console.log(error);
                        alert("Error al actualizar la contacto");
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Cuando se clickea el botón de eliminar, asignar el ID al botón del modal
            $('.deleteApp').click(function() {
                var appId = $(this).attr('data-appid');
                $('#delete-btn').data('appid',
                    appId); // Asignar el ID como un atributo data del botón de eliminar
            });

            // Manejar el evento click del botón de eliminar en el modal
            $('#delete-btn').click(function() {
                var appId = $(this).data('appid');

                $.ajax({
                    url: 'contactos/' +
                        appId, // Asegúrate de ajustar la URL a tu ruta de eliminación
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr(
                            'content') // Asegúrate de tener un meta tag con el CSRF token
                    },
                    success: function(result) {
                        // Aquí puedes recargar la tabla o eliminar la fila del DOM para reflejar el cambio
                        alert("Registro eliminado con éxito");
                        location
                            .reload(); // Opcional: actualiza la página o haz los ajustes necesarios en el DOM
                    },
                    error: function(request, status, error) {
                        alert("No se pudo eliminar el registro");
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#createFormContactos').submit(function(e) {
                e.preventDefault(); // Prevenir la recarga de la página
                var formData = $(this).serialize(); // Serializa los datos del formulario
                // console.log(formData);
                $.ajax({
                    type: "POST",
                    url: "{{ route('contactos.store') }}",
                    data: formData,
                    success: function(response) {
                        alert("Contacto creado con éxito");
                        location.reload();
                    },
                    error: function(error) {
                        console.log(error);
                        alert("Error al crear el contacto");
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#uploadBtn').click(function(e) {
                e.preventDefault(); // Evitar la recarga de la página

                var fileInput = $('#fileUpload')[0];
                if (fileInput.files.length === 0) {
                    $('#uploadError').text('Por favor, selecciona un archivo.').show();
                    return;
                }

                var formData = new FormData();
                formData.append('file', fileInput.files[0]);

                $.ajax({
                    url: 'upload-contactos',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false, // Evitar que jQuery procese los datos
                    contentType: false, // Evitar que jQuery establezca el tipo de contenido
                    success: function(response) {
                        console.log(response);
                        location
                            .reload();
                        // Aquí puedes manejar el cierre de tu modal o diálogo y actualizar la vista según sea necesario
                        // Por ejemplo, si estás usando Bootstrap Modal puedes cerrarlo así:
                        // $('#createAppModal').modal('hide');
                        // Y si quieres llamar a otra función para mostrar los datos actualizados puedes hacerlo aquí
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al subir archivo:', error);
                        $('#uploadError').text('Error al subir el archivo.').show();
                        // location
                        //     .reload();
                    }
                });
            });
        });
    </script>


@stop
