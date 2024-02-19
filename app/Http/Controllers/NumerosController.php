<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Numeros;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class NumerosController extends Controller
{
    public function index(Request $request)
    {
        $numeros = Numeros::all();
        $aplicaciones = Aplicaciones::all();
        return view('numeros/index', [
            'numeros' => $numeros,
            'aplicaciones' => $aplicaciones,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required',
            'numero'        => 'required',
            'id_telefono'   => 'required',
            'aplicacion_id'    => 'required',
            'calidad'       => 'required'
        ]);

        $aplicacion = Aplicaciones::findOrFail($request->aplicacion_id);

        $numero = $aplicacion->numeros()->create([
            'nombre'      => $request->nombre,
            'numero'      => $request->numero,
            'id_telefono' => $request->id_telefono,
            'calidad'     => $request->calidad,
        ]);

        return response()->json(['success' => 'Número creado con éxito.']);


        // Numeros::create($request->all());

        // return response()->json(['success' => 'Numero creado con éxito.']);

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'        => 'required',
            'numero'        => 'required',
            'id_telefono'   => 'required',
            'aplicacion'    => 'required',
            'calidad'       => 'required'
        ]);

        $numero = Numeros::findOrFail($id);
        $numero->update($request->all());

        return response()->json(['success' => 'Numero actualizado con éxito.']);
    }

    public function destroy($id)
    {
        $numero = Numeros::find($id);
        if ($numero) {
            $numero->delete();
            return response()->json(['success' => 'Registro eliminado con éxito.']);
        } else {
            return response()->json(['error' => 'El registro no se encontró.'], 404);
        }
    }
}
