@extends('adminlte::page')

@section('title', 'Hablar con OpenAI')

@section('content_header')
    <h1>Hablar con OpenAI</h1>
@stop

@section('content')
    <form method="POST" action="{{ route('openai.response') }}">
        @csrf
        <div class="form-group">
            <label for="prompt">Escribe un mensaje:</label>
            <input type="text" class="form-control" id="prompt" name="prompt" required>
        </div>
        <button type="submit" class="btn btn-primary">Enviar</button>
    </form>

    @isset($response)
        <div class="mt-3">
            <strong>Respuesta:</strong>
            <p>{{ $response }}</p>
        </div>
    @endisset
@endsection
