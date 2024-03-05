<?php

namespace App\Console\Commands;

use App\Models\Envio;
use App\Jobs\SendMessage;
use App\Models\TareaProgramada;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SendTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:task {--scheduled}';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Envios masivos de mensajes programados';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->info('Ejecutando tarea programada...');
        if ($this->option('scheduled')) {
            // Obtener tareas programadas pendientes para enviar
            $tareasPendientes = TareaProgramada::where('fecha_programada', '<=', now())
                ->where('status', 'pendiente')
                ->get();
            // \Log::info($tareasPendientes);


            foreach ($tareasPendientes as $tarea) {
                $nombreArchivo = basename($tarea->numeros);  // Extrae el nombre del archivo de la ruta completa
                $rutaArchivo = storage_path("app/tareas/$nombreArchivo");  // Construye la ruta completa
                $payload = json_decode($tarea->payload, true);


                try {
                    // Normaliza la ruta para manejar diferencias entre sistemas operativos
                    $rutaArchivo = realpath($rutaArchivo);

                    if ($rutaArchivo !== false && file_exists($rutaArchivo)) {
                        $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                        foreach ($lineas as $linea) {
                            // $linea contiene el contenido de cada línea
                            // Puedes realizar las operaciones necesarias con cada línea aquí
                            $payload['to'] = $linea;
                            SendMessage::dispatch($tarea->token_app, $tarea->phone_id, $payload, $tarea->body, $tarea->messageData);

                        }


                        // Resto de tu lógica aquí...
                        $contacto = new Envio();
                        $contacto->nombrePlantilla = $payload['template']['name'];
                        $contacto->numeroDestinatarios = count($lineas);
                        $contacto->body = $tarea->body;
                        $contacto->save();
                    } else {
                        \Log::error("El archivo no existe en la ruta: $rutaArchivo");
                    }
                    // Resto de tu lógica aquí...
                } catch (\Exception $e) {
                    \Log::error("Error al abrir el archivo: " . $e->getMessage());
                }


                // Actualizar el estado de la tarea a "enviada" o algo similar
                $tarea->status = 'enviada';
                $tarea->save();

            }
        } else {
            $this->info('El comando debe ejecutarse solo cuando hay tareas programadas.');
        }

        $this->info('Tarea programada completada.');
    }

}
