@extends('layouts.app') <!-- O tu layout principal -->

@section('content')
<div class="container">
    <h2>Información del Asistente</h2>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabla para mostrar los detalles del bot -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>OpenAI Key</th>
                <th>OpenAI Org</th>
                <th>OpenAI Assistant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $bot->id }}</td>
                <td>{{ $bot->nombre }}</td>
                <td>{{ $bot->descripcion }}</td>
                <td>{{ $bot->openai_key }}</td>
                <td>{{ $bot->openai_org }}</td>
                <td>{{ $bot->openai_assistant }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Botón para volver a la página anterior -->
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
