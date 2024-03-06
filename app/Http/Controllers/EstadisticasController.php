<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EstadisticasController extends Controller
{
    public function index()
    {

        return view('estadisticas.index');
    }

    public function getStatistics(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
        ]);

        try {
            $startDate = $validatedData['fechaInicio'];
            $endDate = $validatedData['fechaFin'];

            // Obtener estadísticas según el rango de fechas
            $statistics = Message::whereBetween('created_at', [$startDate, $endDate])
                ->where('outgoing', 1) // Agrega cualquier filtro adicional necesario
                ->select('status') // Selecciona solo el campo 'status'
                ->get();

            $totalMessages = $statistics->count();

            // Calcular el número de mensajes para cada estado
            $sentCount = $statistics->where('status', 'sent')->count();
            $deliveredCount = $statistics->where('status', 'delivered')->count();
            $readCount = $statistics->where('status', 'read')->count();
            $failedCount = $statistics->where('status', 'failed')->count();

            //Verificar si $sentCount es cero antes de calcular el porcentaje
            if ($sentCount != 0 && $totalMessages != 0) {
                $sentPercentage = number_format(($sentCount / $totalMessages) * 100, 2);
            } else {
                $sentPercentage = 0;
            }

            // Repite lo mismo para $deliveredCount, $readCount y $failedCount
            // Verificar si $deliveredCount es cero antes de calcular el porcentaje
            if ($deliveredCount != 0 && $totalMessages != 0) {
                $deliveredPercentage = number_format(($deliveredCount / $totalMessages) * 100, 2);
            } else {
                $deliveredPercentage = 0;
            }

            // Verificar si $readCount es cero antes de calcular el porcentaje
            if ($readCount != 0 && $totalMessages != 0) {
                $readPercentage = number_format(($readCount / $totalMessages) * 100, 2);
            } else {
                $readPercentage = 0;
            }

            // Verificar si $failedCount es cero antes de calcular el porcentaje
            if ($failedCount != 0 && $totalMessages != 0) {
                $failedPercentage = number_format(($failedCount / $totalMessages) * 100, 2);
            } else {
                $failedPercentage = 0;
            }


            // Devolver la respuesta JSON con los resultados
            return response()->json([
                'totalMessages' => $totalMessages,
                'sentPercentage' => $sentPercentage,
                'deliveredPercentage' => $deliveredPercentage,
                'readPercentage' => $readPercentage,
                'failedPercentage' => $failedPercentage,
                'sentCount' => $sentCount,
                'deliveredCount' => $deliveredCount,
                'readCount' => $readCount,
                'failedCount' => $failedCount,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);
        } catch (\Exception $e) {
            // Manejar errores
            return response()->json(['error' => 'Error al obtener estadísticas.'], 500);
        }
    }

}
