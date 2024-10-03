<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache; // Importar la clase Cache
use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Obtener el usuario logueado
        $user = Auth::user();

        if (!$user) {
            return redirect('login')->with('error', 'Debe estar logueado para ver las aplicaciones.');
        }

        $hoy = Carbon::now();
        $diaActual = $hoy->day;

        // Definir el rango de fechas para la consulta
        if ($diaActual < 9) {
            // Si el día actual es menor que 9, el rango es desde el 9 del mes anterior hasta el 9 del mes actual
            $fechaInicio = $hoy->copy()->subMonth()->startOfMonth()->addDays(8);
            $fechaFin = $hoy->copy()->startOfMonth()->addDays(8);
        } else {
            // De lo contrario, desde el 9 del mes actual hasta el 9 del mes siguiente
            $fechaInicio = $hoy->copy()->startOfMonth()->addDays(8);
            $fechaFin = $hoy->copy()->addMonth()->startOfMonth()->addDays(8);
        }

        $usernumero = $user->numeros()->first();

        // Tiempo de caché en segundos (5 minutos)
        $cacheTime = 300;

        // Verificar si $usernumero es null
        if ($usernumero !== null) {
            $idTelefono = $usernumero->id_telefono;

            // Cachear el total de mensajes enviados en el mes, utilizando índices para mejorar la consulta
            $totalMensajes = Cache::remember("total_mensajes_{$idTelefono}_{$fechaInicio}_{$fechaFin}", $cacheTime, function () use ($idTelefono, $fechaInicio, $fechaFin) {
                return Message::select('id')  // Solo seleccionamos 'id' ya que estamos contando
                    ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                    ->where('phone_id', $idTelefono)
                    ->where('outgoing', 1)
                    ->count();
            });

            // Cachear el total de mensajes enviados hoy
            $totalMensajesEnviadosHoy = Cache::remember("mensajes_hoy_{$idTelefono}_{$hoy->toDateString()}", $cacheTime, function () use ($idTelefono, $hoy) {
                return Message::select('id')  // Solo seleccionamos 'id'
                    ->whereDate('created_at', $hoy->toDateString())
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
            return $user->contactos()->count(); // Usamos count() directamente en la relación
        });

        // Cachear la cantidad de etiquetas (tags)
        $cantidadTags = Cache::remember("cantidad_tags_{$user->id}", $cacheTime, function () use ($user) {
            return $user->tags()->count(); // Usamos count() directamente en la relación
        });

        return view('home', [
            'totalMensajes' => $totalMensajes,
            'totalMensajesEnviadosHoy' => $totalMensajesEnviadosHoy,
            'cantidadUsuarios' => $cantidadUsuarios,
            'cantidadTags' => $cantidadTags,
        ]);
    }
}
