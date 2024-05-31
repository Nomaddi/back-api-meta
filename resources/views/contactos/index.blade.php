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
                <a data-toggle="modal" data-target="#exportarAppModal" class="btn btn-success btn-sm mb-2" title="Exportar">
                    <i class="fa fa-file-export"></i>
                </a>
            </div>
        </div>
    </div>
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <ul>
                    <li>{{ $error }}</li>
                </ul>
            @endforeach
        </div>
    @endif
    <table id="contactosTable" class="table table-striped tabladatatable dt-responsive" style="width:100%">
        <thead>
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

    @include('contactos.modals.crud-modal')
    @include('contactos.modals.create-modal')
    @include('contactos.modals.delete-modal')
    @include('contactos.modals.import-modal')
    @include('contactos.modals.export-modal')
@endsection
@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.css">
@stop

@section('js')
    <script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#contactosTable').DataTable({
                processing: true,
                serverSide: true, // Aquí se habilita la paginación y búsqueda del lado del servidor
                ajax: '{{ route('contactos.index') }}',
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
                        data: 'tags',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });

        $(document).on('click', '.edit', function(event) {
            event.preventDefault();
            var id = $(this).attr('id');
            $('#form_result').html('');



            $.ajax({
                url: "contactos/edit/" + id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function(data) {
                    $('#nombre').val(data.result.nombre);
                    $('#apellido').val(data.result.apellido);
                    $('#correo').val(data.result.correo);
                    $('#telefono').val(data.result.telefono);
                    $('#notas').val(data.result.notas);
                    $('#hidden_id').val(id);
                    $('.modal-title').text('Editar contacto');
                    $('.editpass').hide();

                    // Seleccionar las etiquetas que ya tiene el contacto
                    var selectedTags = $.map(data.result.tags, function(tag) {
                        return tag.id
                            .toString(); // Convertir a cadena para asegurarse de la comparación de tipos
                    });

                    $('#etiqueta').val(selectedTags);

                    // Rellenar los campos personalizados
                    data.customFields.forEach(function(field) {
                        var value = data.customFieldValues[field.id] || ''; // Valor o vacío
                        $('#custom_field_' + field.id).val(value);
                    });

                    $('#formModal').modal('show');
                },
                error: function(data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            })
        });

        $('#sample_form').on('submit', function(event) {
            event.preventDefault();
            var action_url = '';

            if ($('#action').val() == 'Edit') {
                action_url = "{{ route('contactos.update') }}";
            }

            $.ajax({
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: action_url,
                data: $(this).serialize(),
                dataType: 'json',
                success: function(data) {

                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#sample_form')[0].reset();
                        $('#contactosTable').DataTable().ajax.reload();
                        $('#formModal').modal('hide');
                        Swal.fire('Contacto editado correctamente', data.success, 'success');
                    }
                    $('#form_result').html(html);
                },
                error: function(data) {
                    var errors = data.responseJSON;
                    console.log(errors);
                }
            });
        });

        var user_id;

        $(document).on('click', '.delete', function() {
            user_id = $(this).attr('id');
            $('#confirmModal').modal('show');
        });

        $('#ok_button').click(function() {
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            $.ajax({
                url: "contactos/delete/" + user_id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    setTimeout(function() {
                        $('#confirmModal').modal('hide');
                        $('#contactosTable').DataTable().ajax.reload();
                        Swal.fire('Eliminado', 'Contacto eliminado correctamente', 'success');
                    }, 2000);
                },
                error: function(error) {
                    Swal.fire('Error', 'No se pudo eliminar el contacto', 'error');
                }
            })
        });
    </script>
    <script>
        // Script para agregar validación personalizada usando JavaScript
        document.getElementById('telefono').addEventListener('input', function() {
            var telefonoInput = this;
            var validacion = /^[0-9]{12}$/;

            if (!validacion.test(telefonoInput.value)) {
                telefonoInput.setCustomValidity('Digita el prefijo del país seguido del numero celular');
            } else {
                telefonoInput.setCustomValidity('');
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#createFormContactos').submit(function(e) {
                e.preventDefault(); // Prevenir la recarga de la página
                var formData = $(this).serialize(); // Serializa los datos del formulario

                $.ajax({
                    type: "POST",
                    url: "{{ route('contactos.store') }}",
                    data: formData,
                    success: function(response) {
                        // Si la solicitud es exitosa, maneja la respuesta del servidor
                        $('#contactosTable').DataTable().ajax.reload();
                        $('#createContactoModal').modal('hide');
                        // Resetea el formulario
                        $('#createFormContactos').trigger("reset");
                        // Muestra un mensaje de éxito utilizando la respuesta del servidor
                        Swal.fire('Contacto creado correctamente', response.success, 'success');
                    },
                    error: function(xhr, status, error) {
                        // Si hay un error en la solicitud, muestra el mensaje de error devuelto por el servidor
                        if (xhr.status === 422) {
                            var errorMessage = JSON.parse(xhr.responseText).errors.telefono[0];
                            Swal.fire('Error al crear el contacto', errorMessage, 'error');
                        } else {
                            Swal.fire('Error al crear el contacto',
                                'Ha ocurrido un error durante la creación. Por favor, inténtalo de nuevo más tarde.',
                                'error');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Muestra alerta de éxito
            @if (session('success'))
                Swal.fire({
                    title: 'Éxito!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            @endif

            // Muestra errores uno por uno
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ $error }}',
                        icon: 'error',
                        confirmButtonText: 'Cerrar'
                    });
                @endforeach
            @endif
        });
    </script>
@stop
