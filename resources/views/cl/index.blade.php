@extends('adminlte::page')

@section('title', 'Contratación local')

@section('content_header')
    <h1>Solicitudes de Contratacion local</h1>
@stop

@section('content')

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table id="enviosTable" class="table table-striped table-bordered shadow-lg mt-4 display compact" style="width:100%">
        <thead class="bg-primary text-white">
            <tr>
                <th>No</th>
                <th>Empresa</th>
                <th>Tipo</th>
                <th>Fecha de inicio</th>
                <th>Contrato</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($solicitudes as $app)
                <tr>
                    <th>{{ $app->id }}</th>
                    <th>{{ $app->empresa }}</th>
                    <td>{{ $app->contrato }}</td>
                    <td>{{ $app->fecha_inicio }}</td>
                    <td>
                        <a href="{{ $app->contrato == 'MO' ? env('PROJECT2_URL_MANO') : env('PROJECT2_URL_SERVICIO') }}/{{ $app->id_pdf }}" target="_blank" rel="noopener noreferrer" style="text-decoration: none">
                            {{ $app->codigo_contrato }}
                        </a>
                        <span class="badge {{ $app->estado == 'publicado' ? 'badge-primary' : 'badge-danger' }} badge-pill">{{ $app->estado }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $app->status == 'enviado' ? 'badge-success' : 'badge-warning' }} badge-pill">{{ $app->status }}</span>
                    </td>
                    <td>
                        <a data-toggle="modal" data-target="#modal-show-{{ $app->id }}"
                            class="btn btn-warning btn-sm mb-2" title="Ver">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="{{ route('enviar.solicitud', $app->id) }}" class="btn btn-primary btn-sm mb-2">
                            <i class="fa fa-paper-plane"></i>
                        </a>
                    </td>
                </tr>
                {{-- modal show --}}
                @include('cl.modals.show-modal')
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
        new DataTable('#enviosTable', {
            "order": [
                [0, "desc"]
            ] // Ordenar por la primera columna (created_at) de manera descendente
        });
    </script>


@stop
