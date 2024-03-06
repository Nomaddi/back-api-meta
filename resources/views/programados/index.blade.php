@extends('adminlte::page')

@section('title', 'Envios programados')

@section('content_header')
    <h1>Envios programados</h1>
@stop

@section('content')

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <table id="programadosTable" class="table table-striped table-bordered shadow-lg mt-4 display compact" style="width:100%">
        <thead class="bg-primary text-white">
            <tr>
                <th>No</th>
                <th>Nombre plantilla</th>
                <th>lista enviada</th>
                <th>Mensaje</th>
                <th>Estado</th>
                <th>Programado</th>
                <th>Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($tareas as $app)
                <tr>
                    <th>{{ $app->id }}</th>
                    @php
                        $jsonString = $app->payload;
                        $data = json_decode($jsonString);
                    @endphp
                    <th>{{ $data->template->name }}</th>
                    <td>
                        <a href="{{ route('descargar-archivo', ['id' => $app->id]) }}" class="btn btn-primary">Descargar</a>
                    </td>
                    <td title="{{ $app->body }}">{{ Str::limit($app->body, 50) }}</td>
                    <td>
                        @if ($app->status == 'pendiente')
                            <span class="badge bg-warning">Pendiente</span>
                        @elseif ($app->status == 'enviada')
                            <span class="badge bg-success">Enviado</span>
                        @elseif ($app->status == 'cancelada')
                            <span class="badge bg-danger">Cancelado</span>
                        @else
                            {{ $app->status }}
                        @endif
                    </td>
                    <td>{{ $app->fecha_programada }}</td>
                    <td>{{ $app->created_at }}</td>
                    <td>
                        @if ($app->status == 'pendiente')
                            <a data-toggle="modal" data-target="#modal-show-{{ $app->id }}"
                                class="btn btn-warning btn-sm mb-2" title="Ver">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endif
                    </td>
                </tr>
                {{-- modal show --}}
                @include('programados.modals.show-modal')
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
        new DataTable('#programadosTable', {
            "order": [[ 0, "desc" ]] // Ordenar por la primera columna (created_at) de manera descendente
        });
    </script>


@stop
