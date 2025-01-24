@extends('adminlte::page')

@section('title', 'Editar Destinatario')

@section('content_header')
    <h1>Editar Destinatario</h1>
@stop

@section('content')
    <div class="container">
        <h3>Editar Destinatario del Grupo: {{ $group->name }}</h3>

        <form action="{{ route('groups.updateRecipient', [$group->id, $recipient->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $recipient->email }}" required>
            </div>
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $recipient->name }}">
            </div>

            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="{{ route('groups.showEmails', $group->id) }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
@endsection
