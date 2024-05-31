@extends('adminlte::page')

@section('title', 'Campos personalizados')

@section('content')
    <div class="row">
        <div class="col-lg-12 my-3">
            <div>
                <button id="createFieldBtn" class="btn btn-primary mb-3">Crear Nuevo Campo</button>
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
    @if ($customFields->isEmpty())
        <p>No hay campos personalizados disponibles.</p>
    @else
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customFields as $field)
                    <tr>
                        <td>{{ $field->name }}</td>
                        <td>{{ $field->type }}</td>
                        <td>
                            <button class="btn btn-warning editFieldBtn" data-id="{{ $field->id }}"
                                data-name="{{ $field->name }}" data-type="{{ $field->type }}">Editar</button>
                            <button class="btn btn-danger deleteFieldBtn" data-id="{{ $field->id }}">Eliminar</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

@endsection
@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Create new field
            $('#createFieldBtn').on('click', function() {
                Swal.fire({
                    title: 'Crear Nuevo Campo',
                    html: '<input id="fieldName" class="swal2-input" placeholder="Nombre">' +
                        '<select id="fieldType" class="swal2-input">' +
                        '<option value="text">Texto</option>' +
                        '<option value="number">Número</option>' +
                        '<option value="date">Fecha</option>' +
                        '<option value="email">Correo Electrónico</option>' +
                        '</select>',
                    focusConfirm: false,
                    showCancelButton: true,
                    preConfirm: () => {
                        const name = $('#fieldName').val();
                        const type = $('#fieldType').val();
                        if (!name || !type) {
                            Swal.showValidationMessage('Por favor, completa todos los campos');
                            return false;
                        }
                        return {
                            name: name,
                            type: type
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('custom_fields.store') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                name: result.value.name,
                                type: result.value.type
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', 'Hubo un problema al crear el campo',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Edit field
            $('.editFieldBtn').on('click', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const type = $(this).data('type');
                Swal.fire({
                    title: 'Editar Campo',
                    html: `<input id="editFieldName" class="swal2-input" value="${name}" placeholder="Nombre">` +
                        `<select id="editFieldType" class="swal2-input">` +
                        `<option value="text" ${type === 'text' ? 'selected' : ''}>Texto</option>` +
                        `<option value="number" ${type === 'number' ? 'selected' : ''}>Número</option>` +
                        `<option value="date" ${type === 'date' ? 'selected' : ''}>Fecha</option>` +
                        `<option value="email" ${type === 'email' ? 'selected' : ''}>Correo Electrónico</option>` +
                        `</select>`,
                    focusConfirm: false,
                    showCancelButton: true,
                    preConfirm: () => {
                        const name = $('#editFieldName').val();
                        const type = $('#editFieldType').val();
                        if (!name || !type) {
                            Swal.showValidationMessage('Por favor, completa todos los campos');
                            return false;
                        }
                        return {
                            name: name,
                            type: type
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `custom_fields/${id}`,
                            method: 'POST', // Usa POST aquí
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'PUT', // Agrega el campo _method con valor PUT
                                name: result.value.name,
                                type: result.value.type
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Hubo un problema al editar el campo', 'error');
                            }
                        });
                    }
                });
            });

            // Delete field
            $('.deleteFieldBtn').on('click', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `custom_fields/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error',
                                    'Hubo un problema al eliminar el campo', 'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@stop
