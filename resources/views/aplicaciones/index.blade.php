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

                        <button class="btn btn-danger btn-sm mb-2 deleteApp" data-appid="{{ $app->id }}">
                            <i class="fa fa-trash"></i>
                        </button>

                    </td>
                </tr>
                {{-- modal show --}}
                @include('aplicaciones.modals.show-modal')
                {{-- modal edit --}}
                @include('aplicaciones.modals.edit-modal')
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
        var table = $('#aplicacionesTable').DataTable({
            responsive: true
        });
    </script>
    <script>
        $(document).ready(function() {
            $('form[id^="editForm-"]').on('submit', function(e) {
                e.preventDefault();
                var appId = this.id.split('-')[1];
                var formData = $(this).serialize();
                var row = table.row($(this).parents('tr'));

                $.ajax({
                    type: "POST",
                    url: "aplicaciones/" + appId,
                    data: formData,
                    success: function(response) {
                        if (response.data) {

                            Swal.fire({
                                icon: 'success',
                                title: '¡Creado!',
                                text: 'Aplicación actualizada con éxito',
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
                                text: 'No se pudo actualizar la aplicación.',
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Error al actualizar la aplicación',
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Escuchar clics en botones de eliminar dentro de la tabla
            $('#aplicacionesTable').on('click', '.deleteApp', function() {
                var appId = $(this).data('appid');
                var row = table.row($(this).parents('tr')); // Encuentra la fila del botón clickeado

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
                            url: 'aplicaciones/' +
                                appId, // Asegúrate de que esta es la URL correcta
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(result) {
                                row.remove().draw();
                                Swal.fire(
                                    'Eliminado!',
                                    'La aplicación ha sido eliminada.',
                                    'success'
                                );
                            },
                            error: function(request, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'No se pudo eliminar la aplicación. ' + request
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
            $('#createForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: "POST",
                    url: "{{ route('aplicaciones.store') }}",
                    data: formData,
                    success: function(response) {
                        if (response.data) {

                            Swal.fire({
                                icon: 'success',
                                title: '¡Creado!',
                                text: 'Aplicación creada con éxito',
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
                                text: 'No se pudo obtener la información de la aplicación.',
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al crear la aplicación',
                        });
                    }
                });
            });
        });
    </script>

@stop
