<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Envio;
use App\Models\Message;
use App\Models\Reporte;
use Illuminate\Http\Request;
use App\Exports\MultiSheetExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener estadísticas.'], 500);
        }
    }

    public function exportar($id)
    {
        try {
            $report = Reporte::findOrFail($id);

            $messages = Message::whereBetween('created_at', [$report->fechaInicio, $report->fechaFin])
                ->where('outgoing', 1)
                ->get();

            $plantillas = Envio::select('nombrePlantilla', 'body', 'numeroDestinatarios')
                ->whereBetween('created_at', [$report->fechaInicio, $report->fechaFin])
                ->get();
            // Utiliza la clase de exportación para generar el archivo Excel
            return Excel::download(new MultiSheetExport($messages, $plantillas), 'reporte_' . $report->created_at . '.xlsx');
        } catch (Exception $e) {
            // Maneja el error aquí
            return response()->json(['error' => 'Ocurrió un error al exportar el archivo.'], 500);
        }
    }


}
