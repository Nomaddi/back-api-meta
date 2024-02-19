<?php

namespace App\Http\Controllers;

use App\Libraries\Whatsapp;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use DataTables;

class AplicacionesController extends Controller
{

    public function index(Request $request)
    {
        $aplicaciones = Aplicaciones::all();
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


        Aplicaciones::create($request->all());

        return response()->json(['success' => 'Aplicación creada con éxito.']);
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
        $aplicacion->update($request->all());

        return response()->json(['success' => 'Aplicación actualizada con éxito.']);
    }
    public function destroy($id)
    {
        $aplicacion = Aplicaciones::find($id);
        if ($aplicacion) {
            $aplicacion->delete();
            return response()->json(['success' => 'Registro eliminado con éxito.']);
        } else {
            return response()->json(['error' => 'El registro no se encontró.'], 404);
        }
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
