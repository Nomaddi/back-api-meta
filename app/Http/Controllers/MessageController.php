<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Envio;
use App\Events\Webhook;
use App\Models\Message;
use App\Models\Numeros;
use App\Models\Contacto;
use PhpParser\Node\Expr;
use App\Jobs\SendMessage;
use App\Models\Distintivo;
use App\Libraries\Whatsapp;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use App\Models\TareaProgramada;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ClocalController;


class MessageController extends Controller
{
    public function NumbersApps()
    {
        $numeros = Numeros::all();
        $distintivos = Distintivo::all();
        $tags = Tag::with('contactos')->get();
        return view('plantillas/index', [
            'numeros' => $numeros,
            'tags' => $tags,
            'distintivos' => $distintivos,
        ]);
    }
    public function chat()
    {
        $numeros = Numeros::all();
        $aplicaciones = Aplicaciones::all();

        return view('chat/index', [
            'numeros' => $numeros,
            'aplicaciones' => $aplicaciones
        ]);
    }
    public function index(Request $request)
    {
        $input = $request->all();
        $phone_id = $input['id_phone'];

        try {
            $messages = DB::table('messages', 'm')
                ->where('m.phone_id', $phone_id) // Filtrar por el valor de phone_id
                ->where('m.created_at', '>', Carbon::now()->subDay()) // Filtrar por las últimas 24 horas
                ->where('m.outgoing', '=', '0') // Filtrar por las últimas 24 horas
                ->whereRaw('m.id IN (SELECT MAX(id) FROM messages m2 GROUP BY wa_id)')
                ->orderByDesc('m.id')
                ->limit(30) // Limitar a los 30 primeros registros
                ->get();

            return response()->json([
                'success' => true,
                'data' => $messages,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener chats: ' . json_encode($e->getMessage()));

            // Utilizar el mensaje de la excepción o un mensaje predeterminado
            $errorMessage = isset($e->getMessage()['message']) ? $e->getMessage()['message'] : 'Error interno del servidor';

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'wa_id' => ['required', 'max:20'],
                'body' => ['required', 'string'],
            ]);

            $input = $request->all();
            $wp = new Whatsapp();
            $response = $wp->sendText($input['wa_id'], $input['body'], $input['id_phone'], $input['token_api']);

            $message = new Message();
            $message->wa_id = $input['wa_id'];
            $message->wam_id = $response["messages"][0]["id"];
            $message->phone_id = $input['id_phone'];
            $message->type = 'text';
            $message->outgoing = true;
            $message->body = $input['body'];
            $message->status = 'sent';
            $message->caption = '';
            $message->data = '';
            $message->save();

            return response()->json([
                'success' => true,
                'data' => $message,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al enviar mensaje: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show($waId, Request $request)
    {
        $phone_id = $request->input('id_phone');
        $perPage = 10; // Define cuántos mensajes quieres cargar por página

        try {
            // Obtener los mensajes paginados
            $messagesQuery = DB::table('messages as m')
                ->where('wa_id', $waId)
                ->where('m.phone_id', $phone_id)
                ->orderByDesc('created_at') // Ordena por created_at descendente para obtener los más recientes primero
                ->paginate($perPage);

            $messages = $messagesQuery->getCollection();

            // Procesar cada mensaje si es necesario
            $messages->transform(function ($message) {
                if ($message->type == 'template') {
                    $message->data = unserialize($message->data);
                }
                return $message;
            });

            // Agrupar los mensajes por la fecha de 'created_at'
            $grouped = $messages->groupBy(function ($item) {
                return Carbon::parse($item->created_at)->format('Y-m-d'); // Agrupa por fecha
            });
            // Ordena los mensajes dentro de cada grupo por 'created_at' de manera descendente
            $grouped = $grouped->map(function ($dayMessages) {
                return $dayMessages->sortBy(function ($message) {
                    return Carbon::parse($message->created_at)->timestamp;
                });
            });

            return response()->json([
                'success' => true,
                'data' => $grouped,
                'nextPageUrl' => $messagesQuery->nextPageUrl(), // Proporciona la URL para cargar la próxima página de mensajes
                'prevPageUrl' => $messagesQuery->previousPageUrl(), // Proporciona la URL para la página anterior (si la necesitas)
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener mensajes del chat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }

    public function sendReport($id_report)
    {
        try {
            // Verificar si el usuario está autenticado
            if (Auth::check()) {
                // Obtener el usuario autenticado
                $user = Auth::user();
                // Acceder a la información del usuario

                $token = env('WHATSAPP_API_TOKEN');
                $phoneId = env('WHATSAPPI_API_PHONE_ID');
                $version = 'v18.0';
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $user->phone,
                    'type' => 'template',
                    "template" => [
                        "name" => "reporte_mensual",
                        "language" => [
                            "code" => "es"
                        ],
                        "components" => [
                            [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $user->name

                                    ]
                                ]
                            ],
                            [
                                "type" => "button",
                                'index' => '0',
                                "sub_type" => "url",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $id_report
                                    ]
                                ]
                            ],
                        ]
                    ]
                ];
                $message = Http::withToken($token)->post('https://graph.facebook.com/' . $version . '/' . $phoneId . '/messages', $payload)->throw()->json();
                Log::info('Mensaje enviado correctamente: ', ['data' => $message]);
            }
        } catch (Exception $e) {
            Log::error('Error al enviar mensaje de prueba a jhon: ' . $e->getMessage());
        }
    }

    public function sendMessages($plantilla)
    {
        try {
            // Verificar si el usuario está autenticado
            if (Auth::check()) {
                // Obtener el usuario autenticado
                $user = Auth::user();
                // Acceder a la información del usuario

                $token = env('WHATSAPP_API_TOKEN');
                $phoneId = env('WHATSAPPI_API_PHONE_ID');
                $version = 'v18.0';
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $user->phone,
                    'type' => 'template',
                    "template" => [
                        "name" => "finalizacion_de_envio",
                        "language" => [
                            "code" => "es"
                        ],
                        "components" => [
                            [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $user->name

                                    ]
                                ]
                            ],
                            [
                                "type" => "body",
                                "parameters" => [
                                    [
                                        "type" => "text",
                                        "text" => $plantilla
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                $message = Http::withToken($token)->post('https://graph.facebook.com/' . $version . '/' . $phoneId . '/messages', $payload)->throw()->json();
                Log::info('Mensaje enviado correctamente: ', ['data' => $message]);
            }
        } catch (Exception $e) {
            Log::error('Error al enviar mensaje de prueba a jhon: ' . $e->getMessage());
        }
    }

    public function verifyWebhook(Request $request)
    {
        try {
            $verifyToken = env('WHATSAPP_VERIFY_TOKEN');
            $query = $request->query();

            $mode = $query['hub_mode'];
            $token = $query['hub_verify_token'];
            $challenge = $query['hub_challenge'];

            if ($mode && $token) {
                if ($mode === 'subscribe' && $token == $verifyToken) {
                    return response($challenge, 200)->header('Content-Type', 'text/plain');
                }
            }

            throw new Exception('Invalid request');
        } catch (Exception $e) {
            Log::error('Error al verificar el webhook: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function processWebhook(Request $request)
    {
        try {
            $bodyContent = json_decode($request->getContent(), true);
            $body = '';

            // Determine what happened...
            $value = $bodyContent['entry'][0]['changes'][0]['value'];

            if (!empty($value['statuses'])) {
                $status = $value['statuses'][0]['status']; // sent, delivered, read, failed
                $wam = Message::where('wam_id', $value['statuses'][0]['id'])->first();

                if (!empty($wam->id)) {
                    $wam->status = $status;
                    $wam->save();
                    Webhook::dispatch($wam, true);
                }
                // Si el estado es 'failed', procesar y registrar los detalles del error
                if ($status == 'failed') {
                    $errorMessage = $value['statuses'][0]['errors'][0]['message'] ?? 'Unknown error';
                    $errorCode = $value['statuses'][0]['errors'][0]['code'] ?? 'Unknown code';
                    $errorDetails = $value['statuses'][0]['errors'][0]['error_data']['details'] ?? 'No additional details';

                    // Registrar el error en los logs de Laravel
                    Log::error("Webhook processing error: {$errorMessage}, Code: {$errorCode}, Details: {$errorDetails}");

                    // Aquí podrías agregar lógica adicional si necesitas manejar estos errores de manera específica
                    // Por ejemplo, notificar al equipo de soporte, realizar reintento condicional, etc.
                    if (!empty($wam->id)) {
                        $wam->caption = $errorCode;
                        $wam->save();
                        Webhook::dispatch($wam, true);
                    }
                }
            } else if (!empty($value['messages'])) { // Message
                $exists = Message::where('wam_id', $value['messages'][0]['id'])->first();

                if (empty($exists->id)) {

                    // Verificar si el contacto existe
                    $contacto = Contacto::where('telefono', $value['contacts'][0]['profile']['wa_id'])->first();
                    // Si no existe, crearlo
                    if (!$contacto) {
                        $contacto = new Contacto();
                        $contacto->telefono = $value['contacts'][0]['profile']['wa_id'];
                        $contacto->nombre = $value['contacts'][0]['profile']['name'];
                        $contacto->notas = "Contacto creado automáticamente por webhook";
                        $contacto->save();

                        // Asociar los tags seleccionados al nuevo contacto
                        $contacto->tags()->attach(22);
                    } else if ($contacto->nombre == $contacto->telefono) {
                        $contacto->nombre = $value['contacts'][0]['profile']['name'];
                        $contacto->save();
                    }
                    $mediaSupported = ['audio', 'document', 'image', 'video', 'sticker'];

                    if ($value['messages'][0]['type'] == 'text') {
                        $message = $this->_saveMessage(
                            $value['messages'][0]['text']['body'],
                            'text',
                            $value['messages'][0]['from'],
                            $value['messages'][0]['id'],
                            $value['metadata']['phone_number_id'],
                            $value['messages'][0]['timestamp']
                        );

                        Webhook::dispatch($message, false);
                    } else if (in_array($value['messages'][0]['type'], $mediaSupported)) {
                        $mediaType = $value['messages'][0]['type'];
                        $mediaId = $value['messages'][0][$mediaType]['id'];
                        $wp = new Whatsapp();
                        //consulta para traer token
                        $num = Numeros::where('id_telefono', $value['metadata']['phone_number_id'])->first();

                        $app = Aplicaciones::where('id', $num->aplicacion_id)->first();

                        $tk = $app->token_api;

                        //fin de consulta
                        $file = $wp->downloadMedia($mediaId, $tk);

                        $caption = null;
                        if (!empty($value['messages'][0][$mediaType]['caption'])) {
                            $caption = $value['messages'][0][$mediaType]['caption'];
                        }

                        if (!is_null($file)) {
                            $message = $this->_saveMessage(
                                env('APP_URL') . '/storage/' . $file,
                                $mediaType,
                                $value['messages'][0]['from'],
                                $value['messages'][0]['id'],
                                $value['metadata']['phone_number_id'],
                                $value['messages'][0]['timestamp'],
                                $caption
                            );
                            Webhook::dispatch($message, false);
                        }
                    } else {
                        $type = $value['messages'][0]['type'];
                        if (!empty($value['messages'][0][$type])) {
                            $message = $this->_saveMessage(
                                "($type): \n _" . serialize($value['messages'][0][$type]) . "_",
                                'other',
                                $value['messages'][0]['from'],
                                $value['messages'][0]['id'],
                                $value['metadata']['phone_number_id'],
                                $value['messages'][0]['timestamp']
                            );
                        }
                        Webhook::dispatch($message, false);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $body,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al procesar el webhook: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function loadMessageTemplates(Request $request)
    {
        try {
            $wp = new Whatsapp();
            $token = $request->query('token_api');
            $waba_id = $request->query('id_c_business');
            $templates = $wp->loadTemplates($token, $waba_id);

            return response()->json([
                'success' => true,
                'data' => $templates['data'],
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al cargar las plantillas: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        if ($request->hasFile('pdf')) {
            $pdf = $request->file('pdf');
            $filename = 'pdfs/' . uniqid() . '.' . $pdf->getClientOriginalExtension();

            // Guardar en el disco público
            $path = $pdf->storeAs('', $filename, 'public');

            // Retorna URL del archivo
            return response()->json(['url' => Storage::disk('public')->url($filename)], 200);
        }

        return response()->json(['error' => 'No se pudo subir el archivo.'], 500);
    }

    public function sendMessageTemplate(Request $request)
    {
        try {
            $input = $request->all();
            $wp = new Whatsapp();
            $templateName = $input['template_name'];
            $templateLang = $input['template_language'];
            $tokenApp = $input['token_api'];
            $phone_id = $input['phone_id'];
            $waba_id_app = $input['id_c_business'];
            $fechaProgramada = $input['programar'];
            $distintivo = $input['distintivoSelect'];
            $tags = !empty($input['selectedTags']) ? $input['selectedTags'] : [22];
            $template = $wp->loadTemplateByName($templateName, $templateLang, $tokenApp, $waba_id_app);

            if (!$template) {
                throw new Exception("Invalid template or template not found.");
            }

            $templateBody = '';
            foreach ($template['components'] as $component) {
                if ($component['type'] == 'BODY') {
                    $templateBody = $component['text'];
                }
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'type' => 'template',
                "template" => [
                    "name" => $input['template_name'],
                    "language" => [
                        "code" => $input['template_language']
                    ]
                ]
            ];

            $messageData = [];
            if (!empty($input['header_type']) && !empty($input['header_url'])) {
                $type = strtolower($input['header_type']);
                if ($type == 'document') {
                    $payload['template']['components'][] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => $type,
                                $type => [
                                    "filename" => "Contrato.pdf",
                                    'link' => $input['header_url'],
                                ]
                            ]
                        ],
                    ];
                } else {
                    $payload['template']['components'][] = [
                        'type' => 'header',
                        'parameters' => [
                            [
                                'type' => $type,
                                $type => [
                                    'link' => $input['header_url'],
                                ]
                            ]
                        ],
                    ];
                }
                $messageData = [
                    'header_type' => $input['header_type'],
                    'header_url' => $input['header_url'],
                ];
            }


            $body = $templateBody;
            if (!empty($input['body_placeholders'])) {
                $bodyParams = [];
                foreach ($input['body_placeholders'] as $key => $placeholder) {
                    $bodyParams[] = ['type' => 'text', 'text' => $placeholder];
                    $body = str_replace('{{' . ($key + 1) . '}}', $placeholder, $body);
                }
                $payload['template']['components'][] = [
                    'type' => 'body',
                    'parameters' => $bodyParams,
                ];
            }

            if (!empty($input['buttons_url'])) {
                $payload['template']['components'][] = [
                    'type' => 'button',
                    'index' => '0',
                    'sub_type' => 'url',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => $input['buttons_url'],

                        ]
                    ],
                ];
            }

            $recipients = explode("\n", $input['recipients']);

            if ($fechaProgramada !== null) {
                $fechaFormateada = Carbon::parse($fechaProgramada)->toDateTimeString();
                $numeros = $input['recipients'];
                $fechaHoraActual = Carbon::now()->format('Ymd_His');
                $rutaArchivo = "tareas/tarea_{$fechaHoraActual}.txt";
                Storage::put($rutaArchivo, $numeros);

                $tarea = new TareaProgramada();
                $tarea->token_app = $tokenApp;
                $tarea->phone_id = $phone_id;
                $tarea->numeros = $rutaArchivo;
                $tarea->payload = json_encode($payload);
                $tarea->body = $body;
                $tarea->messageData = json_encode($messageData);
                $tarea->status = 'pendiente';
                $tarea->fecha_programada = $fechaFormateada;
                $tarea->tag = $tags;
                $tarea->distintivo = $distintivo;
                $tarea->save();

                // Ejecutar el comando SendTask con la opción --scheduled
                Artisan::call('send:task', ['--scheduled' => true]);

                return response()->json([
                    'success' => true,
                    'data' => ' Mensajes agregado al cron correctamente.',
                ], 200);

            } else {
                $envio = new Envio([
                    'nombrePlantilla' => $templateName,
                    'numeroDestinatarios' => count($recipients),
                    'status' => 'Pendiente',
                    'sent_messages' => 0,
                    'body' => $body,
                    'tag' => $tags
                ]);
                $envio->save();
                if ($envio->id) {
                    foreach ($recipients as $recipient) {
                        $phone = (int) filter_var($recipient, FILTER_SANITIZE_NUMBER_INT);
                        $payload['to'] = $phone;
                        //aqui se crea el usuario si no existe
                        // Verifica si el contacto existe en la base de datos
                        $contacto = Contacto::where('telefono', $phone)->first();

                        // Si el contacto no existe, créalo
                        if (!$contacto) {
                            $contacto = new Contacto();
                            $contacto->telefono = $phone;
                            $contacto->nombre = $phone;
                            $contacto->notas = "Contacto creado automáticamente por colas";
                            $contacto->save();

                            // Asociar los tags seleccionados al nuevo contacto
                            $contacto->tags()->attach($tags);
                        }
                        SendMessage::dispatch($tokenApp, $phone_id, $payload, $body, $messageData, $distintivo, $envio->id);
                    }
                } else {
                    Log::error('Error al guardar el objeto Envio');
                }

                Log::info('envio encolado' . count($recipients));
            }

            //Cambiar status de pendiente a enviado en contratacion local
            if (!empty($input['status_send'])) {
                // Crear una instancia de ClocalController
                $clocalController = new ClocalController();

                // Llamar al método update de ClocalController
                $clocalController->update($input['solicitudId'], $input['status_send']);

            }

            return response()->json([
                'success' => true,
                'data' => ' Mensajes encolado correctamente.',
            ], 200);
        } catch (Exception $e) {
            Log::info('Error al obtener mensajes8: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function _saveMessage($message, $messageType, $waId, $wamId, $phoneId, $timestamp = null, $caption = null, $data = '')
    {
        $wam = new Message();
        $wam->body = $message;
        $wam->outgoing = false;
        $wam->type = $messageType;
        $wam->wa_id = $waId;
        $wam->wam_id = $wamId;
        $wam->phone_id = $phoneId;
        $wam->status = 'sent';
        $wam->caption = $caption;
        $wam->data = $data;

        if (!is_null($timestamp)) {
            $wam->created_at = Carbon::createFromTimestamp($timestamp)->toDateTimeString();
            $wam->updated_at = Carbon::createFromTimestamp($timestamp)->toDateTimeString();
        }
        $wam->save();

        return $wam;
    }


}
