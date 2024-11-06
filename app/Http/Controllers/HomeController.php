<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('login')->with('error', 'Debe estar logueado para ver las aplicaciones.');
        }

        $hoy = Carbon::now();
        $diaActual = $hoy->day;

        // Definir el rango de fechas para la consulta
        $fechaInicio = ($diaActual < 9)
            ? $hoy->copy()->subMonth()->startOfMonth()->addDays(8)
            : $hoy->copy()->startOfMonth()->addDays(8);

        $fechaFin = ($diaActual < 9)
            ? $hoy->copy()->startOfMonth()->addDays(8)
            : $hoy->copy()->addMonth()->startOfMonth()->addDays(8);

        $usernumero = $user->numeros()->first();
        $cacheTime = 3600; // Cache extendido a una hora

        if ($usernumero !== null) {
            $idTelefono = $usernumero->id_telefono;

            // Total de mensajes enviados en el mes
            $totalMensajes = Cache::remember("total_mensajes_{$idTelefono}_{$fechaInicio}_{$fechaFin}", $cacheTime, function () use ($idTelefono, $fechaInicio, $fechaFin) {
                return Message::whereBetween('created_at', [$fechaInicio, $fechaFin])
                    ->where('phone_id', $idTelefono)
                    ->where('outgoing', 1)
                    ->count();
            });

            // Total de mensajes enviados hoy
            $totalMensajesEnviadosHoy = Cache::remember("mensajes_hoy_{$idTelefono}_{$hoy->toDateString()}", $cacheTime, function () use ($idTelefono, $hoy) {
                return Message::whereDate('created_at', $hoy->toDateString())
                    ->where('phone_id', $idTelefono)
                    ->where('outgoing', 1)
                    ->count();
            });
        } else {
            $totalMensajes = 0;
            $totalMensajesEnviadosHoy = 0;
        }

        // Cachear la cantidad de usuarios (contactos)
        $cantidadUsuarios = Cache::remember("cantidad_usuarios_{$user->id}", $cacheTime, function () use ($user) {
            return $user->contactos()->count();
        });

        // Cachear la cantidad de etiquetas (tags)
        $cantidadTags = Cache::remember("cantidad_tags_{$user->id}", $cacheTime, function () use ($user) {
            return $user->tags()->count();
        });

        return view('home', [
            'totalMensajes' => $totalMensajes,
            'totalMensajesEnviadosHoy' => $totalMensajesEnviadosHoy,
            'cantidadUsuarios' => $cantidadUsuarios,
            'cantidadTags' => $cantidadTags,
        ]);
    }
}
