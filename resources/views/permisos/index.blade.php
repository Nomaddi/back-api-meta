@extends('adminlte::page')

@section('title', 'Administración de permisos')

@section('content_header')
    <h1>Administración de permisos</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-12 my-3">
            <div class="d-flex justify-content-end">
                <a id="triggerCreateForm" class="btn btn-primary btn-sm mb-2" title="Crear">
                    <i class="fa fa-plus-circle"></i>
                </a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table id="permisosTable" class="table table-striped table-bordered shadow-lg mt-4 display compact" style="width:100%">
        <thead class="bg-primary text-white">
            <tr>
                <th>No</th>
                <th>Nombre</th>
                <th>Descripcion</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($permisos as $app)
                <tr>
                    <th>{{ $app->id }}</th>
                    <th>{{ $app->name }}</th>
                    <th>{{ $app->description }}</th>
                    <td>
                        <button class="btn btn-danger btn-sm mb-2 deleteApp" data-appid="{{ $app->id }}"
                            data-toggle="modal" data-target="#deleteConfirmationModal"><i class="fa fa-trash"></i></button>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
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
        new DataTable('#permisosTable');
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#permisosTable').DataTable(); // Inicializar DataTables

            // Delegación de eventos para manejar clics en botones de eliminación
            $('#permisosTable tbody').on('click', '.deleteApp', function() {
                var appId = $(this).data('appid');
                var row = $(this).closest('tr'); // Guarda la fila para poder eliminarla después

                console.log("Captured App ID:", appId); // Debería imprimir el ID correctamente

                // Usando SweetAlert2 para confirmación
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¿Estás seguro de que deseas eliminar este registro?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'permisos/' + appId,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(result) {
                                Swal.fire(
                                    'Eliminado!',
                                    'El registro ha sido eliminado.',
                                    'success'
                                );
                                table.row(row).remove()
                                    .draw(); // Actualizar la tabla sin recargar la página usando la fila guardada
                            },
                            error: function(request, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'No se pudo eliminar el registro: ' + request
                                    .responseText,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            var table = $('#permisosTable').DataTable(); // Asegúrate de que esta es la tabla correcta

            $('#triggerCreateForm').on('click', function() {
                Swal.fire({
                    title: 'Crear Nuevo Permiso',
                    html: `
                        <input type="text" id="nombre" class="swal2-input" placeholder="Nombre del permiso" required>
                        <input type="text" id="description" class="swal2-input" placeholder="Descripción del permiso" required>
                    `,
                    focusConfirm: false,
                    preConfirm: () => {
                        const nombre = Swal.getPopup().querySelector('#nombre').value;
                        const description = Swal.getPopup().querySelector('#description').value;
                        if (!nombre || !description) {
                            Swal.showValidationMessage("Todos los campos son necesarios");
                            return false;
                        }
                        return {
                            nombre: nombre,
                            description: description
                        };
                    },
                    confirmButtonText: 'Guardar',
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('permisos.store') }}", // Asegúrate de que esta es la ruta correcta
                            data: {
                                nombre: result.value.nombre,
                                description: result.value.description,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Asegúrate de que la respuesta incluye todos los datos necesarios
                                table.row.add([
                                    response.data.id, // ID del permiso
                                    response.data.name, // Nombre del permiso
                                    response.data
                                    .description, // Descripción del permiso
                                    `<button class="btn btn-danger btn-sm mb-2 deleteApp" data-appid="${response.data.id}" data-toggle="modal" data-target="#deleteConfirmationModal"><i class="fa fa-trash"></i></button>`
                                ]).draw(false);

                                Swal.fire('¡Creado!', 'Permiso creado con éxito.',
                                    'success');
                            },
                            error: function(error) {
                                Swal.fire('Error', 'Error al crear el permiso: ' + error
                                    .responseText, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>

@stop
