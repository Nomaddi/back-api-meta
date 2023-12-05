<?php

namespace App\Http\Controllers;

use App\Libraries\Whatsapp;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class AplicacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $apps = Aplicaciones::get();
        return $apps;
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
            $apps = new Aplicaciones();
            $apps->nombre = $request->nombre;
            $apps->id_app = $request->id_app;
            $apps->id_c_business = $request->id_c_business;
            $apps->token_api = $request->token_api;
            $apps->save();

            return response()->json([
                'success' => true,
                'data' => $apps,
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
     * @param  \App\Models\Aplicaciones  $aplicaciones
     * @return \Illuminate\Http\Response
     */
    public function show(Aplicaciones $aplicaciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Aplicaciones  $aplicaciones
     * @return \Illuminate\Http\Response
     */
    public function edit(Aplicaciones $aplicaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Aplicaciones  $aplicaciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $aplicacion = Aplicaciones::findOrFail($request->id);
            $aplicacion->nombre = $request->nombre;
            $aplicacion->id_app = $request->id_app;
            $aplicacion->id_c_business = $request->id_c_business;
            $aplicacion->token_api = $request->token_api;
            $aplicacion->save();

            return response()->json([
                'success' => true,
                'data' => $aplicacion,
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
     * @param  \App\Models\Aplicaciones  $aplicaciones
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $aplicacion = Aplicaciones::findOrFail($id);
            $aplicacion->delete();

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
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
