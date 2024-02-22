<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstadisticasController extends Controller
{
    public function index(){
        return view('estadisticas.index');
    }

    public function getStatistics(Request $request)
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            // Obtener todos los datos en el rango de fechas sin aplicar el filtro
            $statistics = Message::whereBetween('created_at', [$startDate, $endDate])
                ->where('outgoing', 1)
                ->select('wa_id', 'type', 'status', 'created_at')
                ->get();

            if ($statistics->isEmpty()) {
                return response()->json(['message' => 'No hay datos disponibles para el rango de fechas proporcionado.']);
            }

            return response()->json(['statistics' => $statistics]);
        } catch (\Exception $e) {
            Log::error('Error al obtener mensajes9: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener datos.'], 500);
        }
    }
}
