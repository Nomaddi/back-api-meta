<form action="{{ route('ia.pause') }}" method="POST">
    @csrf
    <button type="submit">Pausar IA</button>
</form>

<form action="{{ route('ia.resume') }}" method="POST">
    @csrf
    <button type="submit">Reanudar IA</button>
</form>
