<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permission::all();
        return view('permisos/index', [
            'permisos' => $permisos
        ]);
    }
    public function store(Request $request)
    {
        try {
            $permisos = Permission::create([
                'name' => $request->input('nombre'),
                'description' => $request->input('description')
            ]);

            return response()->json([
                'success' => true,
                'data' => $permisos,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $permiso = Permission::findById($id); // Encuentra el permiso por ID

            // Desvincular el permiso de todos los roles
            $roles = $permiso->roles; // Obtiene todos los roles que tienen este permiso
            foreach ($roles as $role) {
                $role->revokePermissionTo($permiso);
            }

            $permiso->delete(); // Elimina el permiso después de desvincularlo

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado correctamente',
            ], 200);
        } catch (\Spatie\Permission\Exceptions\PermissionDoesNotExist $e) {
            return response()->json([
                'success' => false,
                'message' => 'El permiso no existe',
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el permiso',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



}
