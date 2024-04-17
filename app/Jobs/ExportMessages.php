<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Reporte;
use Illuminate\Bus\Queueable;
use App\Exports\MessagesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        Message::with(['contacto.tags'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('outgoing', 1)
            ->cursor()
            ->each(function ($message) use (&$data) {
                $etiquetas = $message->contacto && !$message->contacto->tags->isEmpty()
                    ? $message->contacto->tags->pluck('nombre')->join(', ')
                    : 'Sin etiquetas';
                $data[] = [
                    'Nombre' => $message->contacto ? $message->contacto->nombre : 'Desconocido',
                    'Teléfono' => $message->contacto ? $message->contacto->telefono : 'Desconocido',
                    'Mensaje' => $message->body,
                    'Estado' => $message->status,
                    'Creado en' => $message->created_at->format('Y-m-d H:i:s'),
                    'Etiquetas' => $etiquetas
                ];
            });

        $fileName = 'messages_export_' . now()->format('Y-m-d_His') . '.xlsx';
        Excel::store(new MessagesExport($data), $fileName, 'local');

        // Actualizar el registro del reporte con la ruta del archivo
        $report = Reporte::find($this->reportId);
        if ($report) {
            $report->archivo = $fileName;
            $report->save();
        }

        // Opcional: aquí podrías despachar otro job para enviar notificaciones de que el archivo está listo, etc.
    }
}
