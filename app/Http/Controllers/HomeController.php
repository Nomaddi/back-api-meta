<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Message;
use App\Models\Contacto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obtener el usuario logueado
        $user = Auth::user();

        // Verificar si hay un usuario logueado
        if (!$user) {
            // Opcional: manejar el caso en que no haya usuario logueado
            // Por ejemplo, redirigir al login o mostrar un mensaje
            return redirect('login')->with('error', 'Debe estar logueado para ver las aplicaciones.');
        }

        $hoy = Carbon::now();
        $diaActual = $hoy->day;

        if ($diaActual < 9) {
            // Si el día actual es menor que 9, el rango es desde el 9 del mes anterior hasta el 9 del mes actual
            $fechaInicio = $hoy->copy()->subMonth()->startOfMonth()->addDays(8); // El 9 del mes anterior
            $fechaFin = $hoy->copy()->startOfMonth()->addDays(8); // El 9 del mes actual
        } else {
            // De lo contrario, desde el 9 del mes actual hasta el 9 del mes siguiente
            $fechaInicio = $hoy->copy()->startOfMonth()->addDays(8); // El 9 del mes actual
            $fechaFin = $hoy->copy()->addMonth()->startOfMonth()->addDays(8); // El 9 del mes siguiente
        }

        // Realizar la consulta
        $totalMensajes = Message::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->where('outgoing', 1)
            ->count();

        $totalMensajesEnviadosHoy = Message::whereDate('created_at', $hoy->toDateString())
            ->where('outgoing', 1)
            ->count();

        $cantidadUsuarios = Contacto::count();

        $cantidadTags = Tag::count();

        return view('home', [
            'totalMensajes' => $totalMensajes,
            'totalMensajesEnviadosHoy' => $totalMensajesEnviadosHoy,
            'cantidadUsuarios' => $cantidadUsuarios,
            'cantidadTags' => $cantidadTags
        ]);
    }
}
