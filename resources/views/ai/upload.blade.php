<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir PDF</title>
</head>
<body>
    <h1>Subir PDF para procesar</h1>
    @if (session('success'))
        <div>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('pdf.upload.post') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="pdf">Seleccionar PDF:</label>
            <input type="file" name="pdf" id="pdf" required>
        </div>
        <button type="submit">Subir</button>
    </form>
</body>
</html>
