@extends('adminlte::page')

@section('title', 'Ver contactos con la misma etiqueta')

@section('content')
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="container">
        <div class="container mb-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('tags.index') }}" class="btn btn-secondary">Volver a Etiquetas</a>
            <h2 class="mb-0">Contactos para el Tag: {{ $tag->nombre }}</h2>
        </div>

        <table id="contactsTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <!-- Añade más columnas según necesites -->
                </tr>
            </thead>
            <tbody>
                @foreach ($tag->contactos as $contacto)
                    <tr>
                        <td>{{ $contacto->nombre }}</td>
                        <td>{{ $contacto->telefono }}</td>
                        <td>{{ $contacto->correo }}</td>
                        <!-- Añade más datos según necesites -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.7.0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        new DataTable('#contactsTable');
    </script>
@endsection
