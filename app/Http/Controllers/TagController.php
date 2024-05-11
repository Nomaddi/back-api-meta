<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Obtener todos los tags del usuario autenticado
        $tags = Tag::where('user_id', $user->id)->get();

        return view('tags/index', [
            'tags' => $tags
        ]);
    }
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            // Validar la entrada
            $data = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string|max:500',
                'color' => 'required|string|max:255',
            ]);

            // Crear el tag
            $tag = new Tag;
            $tag->nombre = $data['nombre'];
            $tag->descripcion = $data['descripcion'];
            $tag->color = $data['color'];
            $tag->user_id = $user->id;
            $tag->save();


            return response()->json([
                'success' => true,
                'data' => $tag,
            ], 201);
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
            $user = Auth::user();

            // Buscar el tag y asegurarse de que pertenece al usuario autenticado
            $tag = Tag::where('id', $id)->where('user_id', $user->id)->first();

            // Si el tag no existe o no pertenece al usuario, devolver error
            if (!$tag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag no encontrado o no tienes permiso para modificarlo.'
                ], 404);
            }

            // Validar la entrada
            $data = $request->validate([
                'nombre' => 'sometimes|string|max:255',
                'descripcion' => 'sometimes|string|max:500',
                'color' => 'sometimes|string|max:255'
            ]);

            // Actualizar el tag
            $tag->update($data);

            return response()->json([
                'success' => true,
                'data' => $tag
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $user = Auth::user();
            $tag = Tag::where('id', $request->id)->where('user_id', $user->id)->first();

            if (!$tag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag no encontrado o no tienes permiso para eliminarlo.'
                ], 404);
            }
            // Si no hay contactos ni usuarios relacionados, eliminar la etiqueta
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Etiqueta eliminada correctamente.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function showContacts($tagId)
    {
        $tag = Tag::with('contactos')->find($tagId);

        if (!$tag) {
            return redirect()->route('tags.index')->with('error', 'Tag no encontrado');
        }

        return view('tags.showContacts', ['tag' => $tag]);
    }
}
