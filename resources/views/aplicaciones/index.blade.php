@extends('adminlte::page')

@section('title', 'Aplicaciones')

@section('content')
    <div class="row">
        <div class="col-lg-12 my-3">
            <div class="d-flex justify-content-end">
                <a data-toggle="modal" data-target="#createAppsModal" class="btn btn-primary btn-sm mb-2" title="Crear app">
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
    <table id="aplicacionesTable" class="table table-striped tabladatatable dt-responsive" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nombre</th>
                <th>ID App</th>
                <th>ID C Business</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($aplicaciones as $app)
                <tr>
                    <th>{{ $app->id }}</th>
                    <th>{{ $app->nombre }}</th>
                    <td>{{ $app->id_app }}</td>
                    <td>{{ $app->id_c_business }}</td>
                    <td>
                        <a data-toggle="modal" data-target="#modal-show-{{ $app->id }}"
                            class="btn btn-warning btn-sm mb-2" title="Ver">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a data-toggle="modal" data-target="#modal-edit-{{ $app->id }}"
                            class="btn btn-success btn-sm mb-2" title="Editar">
                            <i class="fa fa-edit"></i>
                        </a>

                        <button class="btn btn-danger btn-sm mb-2 deleteApp" data-appid="{{ $app->id }}"
                            data-toggle="modal" data-target="#deleteConfirmationModal"><i class="fa fa-trash"></i></button>

                    </td>
                </tr>
                {{-- modal show --}}
                @include('aplicaciones.modals.show-modal')
                {{-- modal edit --}}
                @include('aplicaciones.modals.edit-modal')
                <!-- Modal de Confirmación de Eliminación -->
                @include('aplicaciones.modals.delete-modal')
                {{-- modal create --}}
            @endforeach
        </tbody>
    </table>
    @include('aplicaciones.modals.create-modal')
@endsection
@section('css')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.css">
@stop

@section('js')
    <script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
    <script>
        // new DataTable('#aplicacionesTable');
        $('#aplicacionesTable').DataTable({
            responsive: true
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
                    url: "aplicaciones/" + appId, // Ajusta esta URL según tu enrutamiento
                    data: formData,
                    success: function(response) {
                        // $('#modal-show-' + appId).modal('hide'); // Ocultar el modal
                        alert("Aplicación actualizada con éxito"); // Mostrar mensaje de éxito
                        location
                            .reload(); // Opcional: recargar la página o actualizar la vista de alguna otra manera
                    },
                    error: function(error) {
                        console.log(error);
                        alert("Error al actualizar la aplicación");
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
                    url: 'aplicaciones/' +
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
            $('#createForm').submit(function(e) {
                e.preventDefault(); // Prevenir la recarga de la página
                var formData = $(this).serialize(); // Serializa los datos del formulario

                $.ajax({
                    type: "POST",
                    url: "{{ route('aplicaciones.store') }}", // Asegúrate de que esta es la ruta correcta
                    data: formData,
                    success: function(response) {
                        alert("Aplicación creada con éxito"); // Mensaje de éxito
                        location
                            .reload();
                    },
                    error: function(error) {
                        console.log(error);
                        alert("Error al crear la aplicación");
                    }
                });
            });
        });
    </script>
@stop
