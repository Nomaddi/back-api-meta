<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('roles/index', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
    public function store(Request $request)
    {
        try {
            $role = Role::create(['name' => $request->input('nombre')]);

            return response()->json([
                'success' => true,
                'data' => $role,
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
            $role = Role::findById($id);
            $role->syncPermissions($request->permissions);  // Syncing permissions

            return response()->json([
                'success' => true,
                'message' => 'Permisos actualizados correctamente.',
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
        $role = Role::findOrFail($request->id);


        // Si no hay contactos relacionados, elimina la etiqueta
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta eliminada correctamente.',
        ], 200);
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
