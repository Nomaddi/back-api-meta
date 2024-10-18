@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center">Información del Asistente</h2>

    <!-- Mostrar los detalles del asistente -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Instrucciones</th>
                <th>Modelo</th>
                <th>Herramientas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $assistantData['id'] }}</td>
                <td>{{ $assistantData['name'] }}</td>
                <td>{{ $assistantData['instructions'] }}</td>
                <td>{{ $assistantData['model'] }}</td>
                <td>
                    @foreach($assistantData['tools'] as $tool)
                        <span class="badge bg-info">{{ $tool['type'] }}</span>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>

    <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
</div>
@endsection
