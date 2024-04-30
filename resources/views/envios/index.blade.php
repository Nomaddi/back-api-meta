@extends('adminlte::page')

@section('title', 'Envios por plantillas')

@section('content_header')
    <h1>Envios por plantillas</h1>
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
                <th>Nombre plantilla</th>
                <th>N° de envios</th>
                <th>Mensaje</th>
                <th>Etiquetas</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody style="text-align: center">
            @foreach ($envios as $app)
                <tr>
                    <th>{{ $app->id }}</th>
                    <th>{{ $app->nombrePlantilla }}</th>
                    <td>{{ $app->numeroDestinatarios }}</td>
                    <td title="{{ $app->body }}">{{ Str::limit($app->body, 50) }}</td>
                    <td>
                        @if ($app->tag && count($app->tag) > 0)
                            @foreach ($app->tag as $tId)
                                @if (isset($tags[$tId]))
                                    <span
                                        style="background-color: {{ $tags[$tId]->color }}; padding: 3px; border-radius: 4px; font-size: 12px; margin-right: 5px;">
                                        {{ $tags[$tId]->nombre }}
                                    </span>
                                @endif
                            @endforeach
                        @else
                            <span style="padding: 3px; border-radius: 4px; font-size: 12px; margin-right: 5px;">Sin
                                etiqueta</span>
                        @endif
                    </td>
                    <td>{{ $app->created_at }}</td>
                    <td>
                        @if ($app->status == 'Pendiente')
                            <span class="badge badge-warning">{{ $app->status }}</span>
                        @elseif($app->status == 'Completado')
                            <span class="badge badge-success">{{ $app->status }}</span>
                        @else
                            Sin estado
                        @endif
                    </td>
                    <td>
                        <a data-toggle="modal" data-target="#modal-show-{{ $app->id }}"
                            class="btn btn-warning btn-sm mb-2" title="Ver">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
                {{-- modal show --}}
                @include('envios.modals.show-modal')
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
