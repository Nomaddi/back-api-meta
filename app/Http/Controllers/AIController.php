<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Ai;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    /**
     * Mostrar una lista de todas las AIs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Evitamos consultas N+1 cargando la relación de usuarios
        $ais = Ai::with('users')->get();
        return view('ai.index', [   
            'ais' => $ais
        ]);
    }

    /**
     * Almacenar una nueva AI en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        try {
            // Crear la instancia de AI sin validación
            $ai = Ai::create($request->all());
            $user = Auth::user();

        // Asociar el usuario con la IA recién creada
            if ($user) {
                $user->ais()->attach($ai->id);
            }
                // Retornar los datos de la AI creada
                return response()->json(['success' => 'AI creada con éxito.', 'ai' => $ai], 201); // Verifica esta línea
            } catch (\Exception $e) {
                
                return response()->json(['error' => 'Error al crear la AI: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Actualizar una AI existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, $id)
    {
        try {
            // Encontrar la AI y actualizar sin validación
            $ai = Ai::findOrFail($id);
            $ai->update($request->all());

            return response()->json(['success' => 'AI actualizada con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la AI: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una AI de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    
    public function destroy($id)
    {
        try {
            // Encontrar la instancia de AI
            $ai = Ai::findOrFail($id);
            // Eliminar relaciones con los usuarios antes de eliminar la AI
            $ai->users()->detach();
            $ai->delete();

            return redirect()->route('ais.index')->with('success', 'AI eliminada exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('ais.index')->with('error', 'Error al eliminar la AI: ' . $e->getMessage());
        }
    }

}

//APP/HTPP/CONTROLLER/AICONTROLLER.PHP