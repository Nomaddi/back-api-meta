<!-- resources/views/groups/index.blade.php -->
@extends('adminlte::page')

@section('title', 'Envios por plantillas')

@section('content_header')
    <h1>Envios por plantillas</h1>
@stop

@section('content')
    <div class="container">
        <h2>Editar Grupo: {{ $group->name }}</h2>
        <form action="{{ route('groups.update', $group->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Nombre del Grupo</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $group->name }}" required>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $group->description }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Actualizar Grupo</button>
        </form>
    </div>
@endsection
@section('css')
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css"> --}}
@stop

@section('js')
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.js" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script>
        new DataTable('#enviosTable', {
            "order": [
                [0, "desc"]
            ] // Ordenar por la primera columna (created_at) de manera descendente
        });
    </script> --}}


@stop
