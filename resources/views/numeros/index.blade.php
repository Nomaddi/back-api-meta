@extends('adminlte::page')

@section('title', 'Numeros')

@section('content')
    <div class="row">
        <div class="col-lg-12 my-3">
            <div class="d-flex justify-content-end">
                <a data-toggle="modal" data-target="#createAppModal" class="btn btn-primary btn-sm mb-2" title="Crear">
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
    <table id="numerosTable" class="table table-striped table-bordered shadow-lg mt-4 display compact" style="width:100%">
        <thead class="bg-primary text-white">
            <tr>
                <th>No</th>
                <th>Nombre</th>
                <th>Numero</th>
                <th>Calidad</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($numeros as $app)
                <tr>
                    <th>{{ $app->id }}</th>
                    <th>{{ $app->nombre }}</th>
                    <td>{{ $app->numero }}</td>
                    <td>{{ $app->calidad }}</td>
                    <td>
                        <a data-toggle="modal" data-target="#modal-show-{{ $app->id }}"
                            class="btn btn-warning btn-sm mb-2" title="Ver">
                            <i class="fa fa-eye"></i>
                        </a>
                        {{-- <a data-toggle="modal" data-target="#modal-edit-{{ $app->id }}"
                            class="btn btn-success btn-sm mb-2" title="Editar">
                            <i class="fa fa-edit"></i>
                        </a> --}}

                        <button class="btn btn-danger btn-sm mb-2 deleteApp" data-appid="{{ $app->id }}"
                            data-toggle="modal" data-target="#deleteConfirmationModal"><i class="fa fa-trash"></i></button>

                    </td>
                </tr>
                {{-- modal show --}}
                @include('numeros.modals.show-modal')
                <!-- Modal de Confirmación de Eliminación -->
                @include('numeros.modals.delete-modal')
            @endforeach
            {{-- modal create --}}
            @include('numeros.modals.create-modal')
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
        new DataTable('#numerosTable');
    </script>
    <script>
        $(document).ready(function() {
            $('form[id^="editForm-"]').on('submit', function(e) {
                e.preventDefault(); // Evitar la recarga de la página
                var appId = this.id.split('-')[1]; // Obtener el ID de la aplicación
                var formData = $(this).serialize(); // Serializar los datos del formulario

                $.ajax({
                    type: "POST",
                    url: "numeros/" + appId, // Ajusta esta URL según tu enrutamiento
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
                    url: 'numeros/' +
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
                    url: "{{ route('numeros.store') }}", // Asegúrate de que esta es la ruta correcta
                    data: formData,
                    success: function(response) {
                        alert("Aplicación creada con éxito"); // Mensaje de éxito
                        // Aquí puedes elegir recargar la página o actualizar tu tabla/vista con los nuevos datos
                        location
                            .reload();
                    },
                    error: function(error) {
                        console.log(error);
                        alert("Error al crear el numero");
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#selectAplicacion').change(function() {
                var appId = $(this).val();
                var token = $('option:selected', this).data('token');
                var business = $('option:selected', this).data('business');

                if (appId) {
                    $.ajax({
                        url: 'numbers',
                        type: 'GET',
                        data: {
                            id_c_business: business,
                            token_api: token
                        },
                        success: function(response) {
                            var tarjetasHtml = '';
                            response.data.data.forEach(function(numero) {
                                tarjetasHtml += `
                                <div class="card border-success m-3" style="max-width: 18rem;" data-id_telefono="${numero.id}" data-numero="${numero.display_phone_number}" data-calidad="${numero.quality_rating}" data-nombre="${numero.verified_name}">
                                    <div class="card-header">${numero.verified_name}</div>
                                    <div class="card-body">
                                        <p class="card-text">${numero.id}</p>
                                        <p class="card-text">${numero.display_phone_number}</p>
                                        <p class="card-text">Calidad: ${numero.quality_rating}</p>
                                        <a href="#" class="btn btn-primary seleccionarNumero">Seleccionar</a>
                                    </div>
                                </div>
                            `;
                            });
                            $('#tarjetasNumeros').html(tarjetasHtml);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr);
                            alert("Ocurrió un error al obtener la información.");
                        }
                    });
                }
            });
        });

        $(document).on('click', '.seleccionarNumero', function(e) {
            e.preventDefault();
            var card = $(this).closest('.card');

            var id_telefono = card.data('id_telefono');
            var numero = card.data('numero');
            var calidad = card.data('calidad');
            var nombre = card.data('nombre');
            var aplicacion_id = $('#selectAplicacion option:selected').val(); // Nombre de la aplicación seleccionada

            $('#nombre').val(nombre);
            $('#numero').val(numero);
            $('#id_telefono').val(id_telefono);
            $('#aplicacion_id').val(aplicacion_id);
            $('#calidad').val(calidad);

        });
    </script>

@stop
