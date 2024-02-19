<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::with('contactos')->get();
        return view('tags/index', [
            'tags' => $tags
        ]);
    }
    public function store(Request $request)
    {
        try {
            $tags = new Tag();
            $tags->nombre = $request->nombre;
            $tags->descripcion = $request->descripcion;
            $tags->color = $request->color;
            $tags->save();

            return response()->json([
                'success' => true,
                'data' => $tags,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id); // Corrige aquí el uso de $id
            $tag->nombre = $request->nombre;
            $tag->descripcion = $request->descripcion;
            $tag->color = $request->color;
            $tag->save();

            return response()->json([
                'success' => true,
                'data' => $tag,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy(Request $request)
    {
        $tag = Tag::findOrFail($request->id);

        //elimina el tag y de todos los contactos
        // $tag->contactos()->detach();

        //No deja eliminar una etiqueta si un usuario la tiene agregada
        // Verifica si la etiqueta está relacionada con algún contacto
        if ($tag->contactos->count() > 0) {
            // Si hay contactos que dependen de esta etiqueta, muestra un mensaje de advertencia
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la etiqueta porque está relacionada con contactos.',
                'related_contacts' => $tag->contactos, // Puedes enviar información sobre los contactos relacionados
            ], 400);
        }

        // Si no hay contactos relacionados, elimina la etiqueta
        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta eliminada correctamente.',
        ], 200);
    }
}
