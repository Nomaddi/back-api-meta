<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Envio;
use App\Models\Message;
use App\Models\Reporte;
use Illuminate\Http\Request;
use App\Jobs\ExportMessages;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EstadisticasController extends Controller
{
    public function index()
    {
        $reportes = Reporte::all();
        return view('estadisticas.index', [
            'reportes' => $reportes
        ]);
    }

    public function getStatistics(Request $request)
    {
        $validatedData = $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date|after_or_equal:fechaInicio',
        ]);

        try {
            $startDate = $validatedData['fechaInicio'];
            $endDate = $validatedData['fechaFin'];

            $respote = new Reporte();
            $respote->fechaInicio = $startDate;
            $respote->fechaFin = $endDate;
            $respote->save();

            $reportes = Reporte::all();

            // Obtener el conteo de mensajes por estado en un solo query
            $statusCounts = Message::whereBetween('created_at', [$startDate, $endDate])
                ->where('outgoing', 1)
                ->select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->keyBy('status'); // Clavear por el estado para un acceso fácil

            $totalMessages = $statusCounts->sum('count'); // Total de mensajes

            // Función para obtener el porcentaje
            $getPercentage = function ($count) use ($totalMessages) {
                return $totalMessages > 0 ? number_format(($count / $totalMessages) * 100, 2) : 0;
            };

            // Usar la función para calcular los porcentajes
            $sentPercentage = $getPercentage($statusCounts->get('sent', collect(['count' => 0]))['count']);
            $deliveredPercentage = $getPercentage($statusCounts->get('delivered', collect(['count' => 0]))['count']);
            $readPercentage = $getPercentage($statusCounts->get('read', collect(['count' => 0]))['count']);
            $failedPercentage = $getPercentage($statusCounts->get('failed', collect(['count' => 0]))['count']);

            return response()->json([
                'totalMessages' => $totalMessages,
                'sentPercentage' => $sentPercentage,
                'deliveredPercentage' => $deliveredPercentage,
                'readPercentage' => $readPercentage,
                'failedPercentage' => $failedPercentage,
                'sentCount' => $statusCounts->get('sent', collect(['count' => 0]))['count'],
                'deliveredCount' => $statusCounts->get('delivered', collect(['count' => 0]))['count'],
                'readCount' => $statusCounts->get('read', collect(['count' => 0]))['count'],
                'failedCount' => $statusCounts->get('failed', collect(['count' => 0]))['count'],
                'startDate' => $startDate,
                'endDate' => $endDate,
                'reportes' => $reportes,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Error al obtener estadísticas.'], 500);
        }
    }

    public function exportar($id)
    {
        try {
            $report = Reporte::findOrFail($id);
            ExportMessages::dispatch($report->fechaInicio, $report->fechaFin, $id);

            return response()->json(['status' => 'Exportación iniciada']);
        } catch (ModelNotFoundException $e) {
            Log::error("Reporte no encontrado: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Reporte no encontrado.'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            Log::error("Error al exportar mensajes: {$e->getMessage()}", ['exception' => $e]);
            return response()->json(['error' => 'Ocurrió un error al exportar el archivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }




}
