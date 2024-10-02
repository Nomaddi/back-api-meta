@extends('adminlte::page')

@section('title', 'Subir PDF')

@section('content_header')
    <h1>Subir y Procesar PDF</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('openai.pdf') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="pdf">Selecciona un archivo PDF:</label>
            <input type="file" class="form-control" id="pdf" name="pdf" required>
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
