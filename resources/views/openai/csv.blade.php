@extends('adminlte::page')

@section('title', 'Subir CSV')

@section('content_header')
    <h1>Subir y Procesar CSV</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('openai.csv') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="csv">Selecciona un archivo CSV:</label>
            <input type="file" class="form-control" id="csv" name="csv" required>
        </div>
        <button type="submit" class="btn btn-primary">Subir</button>
    </form>

    @isset($response)
        <div class="mt-3">
            <strong>Respuesta:</strong>
            <p>{{ $response }}</p>
        </div>
    @endisset
@endsection
