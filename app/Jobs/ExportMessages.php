<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Reporte;
use Illuminate\Bus\Queueable;
use App\Exports\MessagesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\MessageController;

class ExportMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $startDate;
    protected $endDate;
    protected $reportId;

    public function __construct($startDate, $endDate, $reportId)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reportId = $reportId;
    }

    public function handle()
    {
        try {
            $results = DB::select('CALL GetMessagesReport(?, ?)', [$this->startDate, $this->endDate]);
            if (!empty($results)) {

                $fileName = 'messages_export_' . now()->format('Y-m-d_His') . '.xlsx';
                Excel::store(new MessagesExport($results), $fileName, 'local');

                // Actualizar el registro del reporte con la ruta del archivo
                $report = Reporte::find($this->reportId);
                if ($report) {
                    $report->archivo = $fileName;
                    $report->save();
                }
            } else {
                echo "No se encontraron resultados.";
            }
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }


        // Opcional: aquí podrías despachar otro job para enviar notificaciones de que el archivo está listo, etc.
        $controller = new MessageController();
        $controller->sendReport($this->reportId);
    }
}
