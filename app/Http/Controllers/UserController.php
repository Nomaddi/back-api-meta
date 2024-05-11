<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        return view('user/index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }
    public function store(Request $request)
    {
        try {
            $users = new User();
            $users->nombre = $request->nombre;
            $users->descripcion = $request->descripcion;
            $users->color = $request->color;
            $users->save();

            return response()->json([
                'success' => true,
                'data' => $users,
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
            $user = User::findOrFail($id);
            $user->syncRoles($request->roles);  // Asumiendo que los nombres de los roles son enviados en el request

            return response()->json([
                'success' => true,
                'message' => 'Roles actualizados correctamente para el usuario.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los roles del usuario.',
                'error' => $e->getMessage()
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

    public function showContacts($tagId)
    {
        $tag = Tag::with('contactos')->find($tagId);

        if (!$tag) {
            return redirect()->route('users.index')->with('error', 'Tag no encontrado');
        }

        return view('users.showContacts', ['tag' => $tag]);
    }
}
