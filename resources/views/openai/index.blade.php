@extends('adminlte::page')

@section('title', 'OpenAI')

@section('content_header')
    <h1>Módulo OpenAI</h1>
@stop

@section('content')
    <div class="container">
        <h3>Acciones disponibles</h3>
        <ul>
            <li><a href="{{ route('openai.chat') }}">Hablar con OpenAI</a></li>
            <li><a href="{{ route('openai.csv') }}">Subir CSV</a></li>
            <li><a href="{{ route('openai.pdf') }}">Subir PDF</a></li>
        </ul>
    </div>
@endsection
