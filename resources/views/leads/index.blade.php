@extends('adminlte::page')

@section('title', 'Bots')

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="row">
        <div class="col-lg-12 my-3">
            <div>
                <h2>Leads</h2>
            </div>
        </div>
    </div>
    <table id="leasdTable" class="table table-striped tabladatatable dt-responsive" style="width:100%">
        <thead>
            <tr>
                <th>id</th>
                <th>Bot</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Telefono</th>
                <th>Detalles</th>
                <th>Calificación</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($leads as $lead)
                <tr>
                    <td>{{ $lead->id }}</td>
                    <td>{{ $lead->bot_id }}</td>
                    <td>{{ $lead->nombre }}</td>
                    <td>{{ $lead->email }}</td>
                    <td>{{ $lead->telefono }}</td>
                    <td>{{ $lead->detalles }}</td>
                    <td>
                        @if ($lead->calificacion === 'caliente')
                            <span class="badge badge-success">Caliente</span>
                        @elseif ($lead->calificacion === 'tibio')
                            <span class="badge badge-warning">Tibio</span>
                        @elseif ($lead->calificacion === 'frio')
                            <span class="badge badge-info">Frío</span>
                        @endif
                    </td>
                    <td>
                        <select class="form-select status-select" data-id="{{ $lead->id }}"
                            onchange="updateSelectColor(this)">
                            <option value="nuevo" {{ $lead->estado == 'nuevo' ? 'selected' : '' }}>Nuevo</option>
                            <option value="finalizado" {{ $lead->estado == 'finalizado' ? 'selected' : '' }}>Finalizado
                            </option>
                        </select>
                    </td>
                    <td>{{ $lead->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.css">
    <style>
        /* Color azul para "Nuevo" */
        .status-select.nuevo {
            background-color: #007bff;
            /* Azul */
            color: white;
        }

        /* Color verde para "Finalizado" */
        .status-select.finalizado {
            background-color: #28a745;
            /* Verde */
            color: white;
        }

        /* Estilos adicionales para el texto de las opciones en el menú desplegable */
        .status-select option[value="nuevo"] {
            background-color: #007bff;
            color: white;
        }

        .status-select option[value="finalizado"] {
            background-color: #28a745;
            color: white;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
    <script src="//cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>
    <script>
        var table = $('#leasdTable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
        });
    </script>
    <script>
        function updateSelectColor(selectElement) {
            // Quitar las clases previas de color
            selectElement.classList.remove('nuevo', 'finalizado');

            // Agregar la clase adecuada según el valor seleccionado
            if (selectElement.value === 'nuevo') {
                selectElement.classList.add('nuevo'); // Clase para "Nuevo"
            } else if (selectElement.value === 'finalizado') {
                selectElement.classList.add('finalizado'); // Clase para "Finalizado"
            }
        }

        // Aplicar el color inicial al cargar la página
        document.querySelectorAll('.status-select').forEach(select => {
            updateSelectColor(select);
        });
    </script>
    <script>
        $(document).on('change', '.status-select', function() {
            var estado = $(this).val();
            var id = $(this).data('id');
            var statusUrl = "{{ route('update.status') }}";

            $.ajax({
                url: statusUrl,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    estado: estado
                },
                success: function(response) {
                    if (response.success) {
                        updateSelectColor(document.querySelector(`[data-id="${id}"]`));
                        Swal.fire({
                            icon: 'success',
                            title: 'Estado actualizado',
                            text: response.message,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al actualizar el estado.'
                    });
                }
            });
        });
    </script>
@stop
