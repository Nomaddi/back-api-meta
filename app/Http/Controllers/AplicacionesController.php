<?php

namespace App\Http\Controllers;

use Exception;
use DataTables;
use App\Libraries\Whatsapp;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AplicacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:aplicaciones.index')->only('index');
    }

    public function index(Request $request)
    {
        // Obtener el usuario logueado
        $user = Auth::user();

        // Verificar si hay un usuario logueado
        if ($user) {
            // Obtener las aplicaciones asociadas al usuario logueado
            $aplicaciones = $user->aplicaciones;
        } else {
            // Opcional: manejar el caso en que no haya usuario logueado
            // Por ejemplo, redirigir al login o mostrar un mensaje
            return redirect('login')->with('error', 'Debe estar logueado para ver las aplicaciones.');
        }

        return view('aplicaciones/index', [
            'aplicaciones' => $aplicaciones,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'id_app' => 'required',
            'id_c_business' => 'required',
            'token_api' => 'required',
        ]);


        $aplicacion = Aplicaciones::create($request->all());

        // Obtener el usuario logueado
        $user = Auth::user();

        // Asociar el usuario con la aplicación recién creada
        if ($user) {
            $user->aplicaciones()->attach($aplicacion->id);
        }

        return response()->json([
            'success' => 'Aplicación creada con éxito.',
            'data' => $aplicacion  // Devuelve la instancia de la aplicación creada
        ]);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'id_app' => 'required',
            'id_c_business' => 'required',
            'token_api' => 'required',
        ]);

        $aplicacion = Aplicaciones::findOrFail($id);

        $aplicacion->update([
            'nombre' => $request->nombre,
            'id_app' => $request->id_app,
            'id_c_business' => $request->id_c_business,
            'token_api' => $request->token_api,
        ]);

        return response()->json([
            'success' => 'Aplicación actualizada con éxito.',
            'data' => $aplicacion  // Devuelve la instancia de la aplicación creada
        ]);
    }
    public function destroy($id)
    {
        $aplicacion = Aplicaciones::with('numeros')->findOrFail($id);

        // Verificar si la aplicación tiene números asociados
        if ($aplicacion->numeros->isNotEmpty()) {
            return response()->json(['error' => 'No se puede eliminar la aplicación porque tiene números asociados.'], 400);
        }

        // Si no hay números asociados, proceder a eliminar la aplicación
        $aplicacion->delete();
        return response()->json(['success' => 'Registro eliminado con éxito.']);
    }

    public function Numbers(Request $request)
    {
        try {
            $wp = new Whatsapp();
            $token = $request->query('token_api');
            $waba_id = $request->query('id_c_business');
            $number = $wp->numbersLoad($token, $waba_id);
            return response()->json([
                'success' => true,
                'data' => $number,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener mensajes Aplicaciones 4: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
