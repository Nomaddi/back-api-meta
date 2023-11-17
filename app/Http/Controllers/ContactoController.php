<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Contacto;
use App\Models\Tag;
use App\Imports\ContactosImport;
use Maatwebsite\Excel\Facades\Excel;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contactos = Contacto::with('tags')->get();
        return $contactos;
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
            $contacto = new Contacto();
            $contacto->nombre = $request->nombre;
            $contacto->apellido = $request->apellido;
            $contacto->correo = $request->correo;
            $contacto->telefono = $request->telefono;
            $contacto->notas = $request->notas;
            $contacto->save();

            $tag = Tag::whereIn('id', $request->ciudad)->get();

            if ($tag->count() > 0) {
                $contacto->tags()->attach($tag);
            }

            return response()->json([
                'success' => true,
                'data' => $contacto,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $contacto = Contacto::findOrFail($request->id);
            $contacto->nombre = $request->nombre;
            $contacto->apellido = $request->apellido;
            $contacto->correo = $request->correo;
            $contacto->telefono = $request->telefono;
            $contacto->notas = $request->notas;
            $contacto->save();

            $tagIds = $request->ciudad; // Obtén un array de IDs de etiquetas a asignar al contacto

            $contacto->tags()->sync($tagIds);

            return response()->json([
                'success' => true,
                'data' => $contacto,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $contacto = Contacto::findOrFail($request->id);

        // Elimina las relaciones de muchos a muchos con las etiquetas
        $contacto->tags()->detach();

        // Luego elimina el contacto
        $contacto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contacto eliminado correctamente.',
        ], 200);
    }

    public function uploadUsers(Request $request)
    {
        try {
            Excel::import(new ContactosImport, $request->file);
            return response()->json([
                'success' => true,
                'message' => 'Importación correcta.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la importación: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Añade el trace para obtener más detalles
            ], 500);
        }
    }
}
