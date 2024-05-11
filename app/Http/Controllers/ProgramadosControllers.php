<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TareaProgramada;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProgramadosControllers extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tareas = $user->tareasProgramadas()->select(
            'tarea_programadas.id', // Especifica la tabla de donde se selecciona 'id'
            'numeros',
            'payload',
            'body',
            'status',
            'fecha_programada',
            'tarea_programadas.created_at' // Especifica la tabla para 'created_at' también
        )
            ->orderByDesc('tarea_programadas.created_at') // Especifica la tabla para 'created_at' en el orderBy
            ->get();

        return view(
            'programados.index',
            [
                'tareas' => $tareas
            ]
        );
    }

    public function descargar($id)
    {
        $archivo = TareaProgramada::findOrFail($id);
        $ruta = $archivo->numeros; // La ruta almacenada en la base de datos

        // Genera un nombre de archivo único para el archivo temporal
        $nombreArchivoUnico = Str::random(20) . '_' . basename($ruta);

        // Genera una nueva ruta dentro de la carpeta storage/app/temp para almacenar el archivo temporalmente
        $rutaTemporal = 'temp/' . $nombreArchivoUnico;

        // Copia el archivo desde la ubicación original a la ubicación temporal
        Storage::copy($ruta, $rutaTemporal);

        // Descarga el archivo desde la ubicación temporal
        return Storage::download($rutaTemporal);
    }

    public function actualizarEstado($id)
    {
        $app = TareaProgramada::findOrFail($id);
        // Verificar si la tarea está en estado "pendiente"
        if ($app->status == 'pendiente') {
            // Actualizar el estado solo si está en estado "pendiente"
            $app->status = request('estado');
            $app->save();

            return redirect()->back()->with('success', 'Estado actualizado correctamente');
        } else {
            // Si no está en estado "pendiente", redireccionar con un mensaje de error
            return redirect()->back()->with('error', 'No se puede cambiar el estado de una tarea que no está pendiente');
        }
    }
}
