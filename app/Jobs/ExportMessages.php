<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Reporte;
use Illuminate\Bus\Queueable;
use App\Exports\MessagesExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Http\Controllers\MessageController;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExportMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDate;
    protected $endDate;
    protected $reportId;
    protected $id_telefono;

    public $timeout = 120000;

    public function __construct($startDate, $endDate, $reportId, $id_telefono)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportId = $reportId;
        $this->id_telefono = $id_telefono;
    }

    public function handle()
    {
        try {
            $results = DB::select('CALL GetMessagesReport(?, ?, ?)', [$this->startDate, $this->endDate, $this->id_telefono]);
            if (!empty($results)) {

                $fileName = 'messages_export_' . now()->format('Y-m-d_His') . '.csv';  // Cambio de extensión a CSV
                $filePath = storage_path('app/' . $fileName);

                // Crear archivo CSV
                $handle = fopen($filePath, 'w');

                $headers = ['ID', 'Nombre', 'Numero', 'Estado', 'Distintivo', 'vacio', 'Fecha'];
                fputcsv($handle, $headers);

                foreach ($results as $row) {
                    fputcsv($handle, (array) $row);
                }
                fclose($handle);

                // Actualizar el registro del reporte con la ruta del archivo
                $report = Reporte::find($this->reportId);
                if ($report) {
                    $report->archivo = $fileName;
                    $report->save();
                }
            } else {
                Log::error("No se encontraron resultados.");
            }

        } catch (\Exception $e) {
            Log::error("Error al exportar mensajes: {$e->getMessage()}", ['exception' => $e]);
            throw $e; // Lanzar la excepción para que el trabajo en cola se reintentara
        }

        // Opcional: aquí podrías despachar otro job para enviar notificaciones de que el archivo está listo, etc.
        $controller = new MessageController();
        $controller->sendReport($this->reportId);


    }
}
