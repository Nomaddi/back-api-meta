<?php

namespace App\Jobs;

use App\Libraries\Whatsapp;
use App\Models\Message;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;
    public $body;
    public $messageData;
    public $tokenApp;
    public $phone_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tokenApp, $phone_id, $payload, $body, $messageData = [])
    {
        $this->payload = $payload;
        $this->body = $body;
        $this->messageData = $messageData;
        $this->tokenApp = $tokenApp;
        $this->phone_id = $phone_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $wp = new Whatsapp();
            $request = $wp->genericPayload($this->payload, $this->tokenApp, $this->phone_id);
            if (isset($request["contacts"][0]["wa_id"])) {
                $wam = new Message();
                $wam->body = $this->body;
                $wam->outgoing = true;
                $wam->type = 'template';
                $wam->wa_id = $request["contacts"][0]["wa_id"];
                $wam->wam_id = $request["messages"][0]["id"];
                $wam->phone_id = $this->phone_id;
                $wam->status = 'sent';
                $wam->caption = '';
                $wam->data = serialize($this->messageData);
                $wam->save();
            } else {
                // Encuentra el JSON en el mensaje de error
                $jsonStartPos = strpos($request, '{');
                $errorJsonString = substr($request, $jsonStartPos);
                // Decodifica la cadena JSON a un array
                $errorJson = json_decode($errorJsonString, true);

                // Asegúrate de verificar que la decodificación fue exitosa y que los datos necesarios están presentes
                if ($errorJson && isset($errorJson['error']['code'], $errorJson['error']['fbtrace_id'])) {
                    $errorCode = $errorJson['error']['code'];
                    $fbtrace_id = $errorJson['error']['fbtrace_id'];

                    // Ahora puedes manejar $errorCode y $fbtrace_id según sea necesario
                    $wam = new Message();
                    $wam->body = $this->body;
                    $wam->outgoing = true;
                    $wam->type = 'template';
                    $wam->wa_id = $this->payload["to"];
                    $wam->wam_id = $fbtrace_id;
                    $wam->phone_id = $this->phone_id;
                    $wam->status = 'failed';
                    $wam->caption = $errorCode;
                    $wam->data = serialize($this->messageData);
                    $wam->save();
                } else {
                    // Manejo de error si la respuesta no contiene el formato esperado o la decodificación falló
                    Log::error("Error al procesar la respuesta de error o la respuesta no contiene el formato esperado: " . $errorJsonString);
                }
            }
        } catch (Exception $e) {
            Log::error('error en catch' . $e->getMessage());
        }

    }
}
