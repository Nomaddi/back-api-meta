<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{
    // leads
    public function index()
    {
        // Importar los leads relacionados con los bots del usuario
        $leads = Auth::user()->bots->flatMap->leads;

        return view('leads.index', [
            'leads' => $leads
        ]);
    }

    // updateStatus
    public function updateStatus(Request $request)
    {
        // Validar la solicitud
        $request->validate([
            'id' => 'required|exists:leads,id',
            'estado' => 'required|string'
        ]);

        // Encontrar el lead por su ID y actualizar el estado
        $lead = Lead::find($request->id);
        $lead->estado = $request->estado;

        if ($lead->save()) {
            return response()->json([
                'success' => true,
                'message' => 'El estado del lead se ha actualizado exitosamente.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el estado del lead.'
            ]);
        }
    }
}
