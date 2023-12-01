<?php

namespace App\Http\Controllers;

use App\Models\Numeros;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class NumerosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $numeros = Numeros::get();
        return $numeros;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $numero = new Numeros();
            $numero->nombre = $request->nombre;
            $numero->numero = $request->numero;
            $numero->id_telefono = $request->id_telefono;
            $numero->aplicacion = $request->aplicacion;
            $numero->calidad = $request->calidad;
            $numero->save();

            return response()->json([
                'success' => true,
                'data' => $numero,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Numeros  $numeros
     * @return \Illuminate\Http\Response
     */
    public function show(Numeros $numeros)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Numeros  $numeros
     * @return \Illuminate\Http\Response
     */
    public function edit(Numeros $numeros)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Numeros  $numeros
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $numero = Numeros::findOrFail($request->id);
            $numero->nombre = $request->nombre;
            $numero->numero = $request->numero;
            $numero->id_telefono = $request->id_telefono;
            $numero->aplicacion = $request->aplicacion;
            $numero->calidad = $request->calidad;
            $numero->save();

            return response()->json([
                'success' => true,
                'data' => $numero,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Numeros  $numeros
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $numero = Numeros::findOrFail($id);
            $numero->delete();

            return response()->json([
                'success' => true,
                'message' => 'Aplicación eliminada correctamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aplicación no encontrada.',
            ], 404);
        }
    }
}
