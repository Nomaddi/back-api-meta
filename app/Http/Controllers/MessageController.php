<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\Tag;
use App\Events\Webhook;
use App\Models\Message;
use App\Models\Numeros;
use PhpParser\Node\Expr;
use App\Jobs\SendMessage;
use App\Libraries\Whatsapp;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


class MessageController extends Controller
{
    public function NumbersApps()
    {
        $numeros = Numeros::all();
        $tags = Tag::with('contactos')->get();
        return view('plantillas/index', [
            'numeros' => $numeros,
            'tags' => $tags,
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $phone_id = $input['id_phone'];

        try {
            $messages = DB::table('messages', 'm')
                ->where('m.phone_id', $phone_id) // Filtrar por el valor de phone_id
                ->whereRaw('m.id IN (SELECT MAX(id) FROM messages m2 WHERE m2.phone_id = ? GROUP BY wa_id)', [$phone_id])
                ->where('m.created_at', '>', Carbon::now()->subDay()) // Filtrar por las últimas 24 horas
                ->where('m.outgoing', '=', 0) // Agregar condición para outgoing igual a 0
                ->orderByDesc('m.id')
                ->get();
            // $messages = DB::table('messages', 'm')
            //     ->where('m.phone_id', $phone_id) // Filtrar por el valor de phone_id
            //     ->where('m.created_at', '>', Carbon::now()->subDay()) // Filtrar por las últimas 24 horas
            //     ->whereRaw('m.id IN (SELECT MAX(id) FROM messages m2 GROUP BY wa_id)')
            //     ->orderByDesc('m.id')
            //     ->get();

            return response()->json([
                'success' => true,
                'data' => $messages,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener mensajes1: ' . json_encode($e->getMessage()));

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
            Log::error('Error al obtener mensajes2: ' . $e->getMessage());
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
        $input = $request->all();
        $phone_id = $input['id_phone'];
        try {
            $messages = DB::table('messages', 'm')
                ->where('wa_id', $waId)
                ->where('m.phone_id', $phone_id) // Filtrar por el valor de phone_id
                ->orderBy('created_at')
                ->get();

            foreach ($messages as $key => $message) {
                if ($message->type == 'template') {
                    $message->data = unserialize($message->data);
                }
                $messages[$key] = $message;
            }

            return response()->json([
                'success' => true,
                'data' => $messages,
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener mensajes3: ' . $e->getMessage());
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
            Log::error('Error al obtener mensajes5: ' . $e->getMessage());
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
            } else if (!empty($value['messages'])) { // Message
                $exists = Message::where('wam_id', $value['messages'][0]['id'])->first();

                if (empty($exists->id)) {
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

                        $app = Aplicaciones::where('id', $num->aplicacion)->first();

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
            Log::error('Error al obtener mensajes6: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendMessages()
    {
        // try {
        $token = env('WHATSAPP_API_TOKEN');
        $phoneId = env('WHATSAPPI_API_PHONE_ID');
        $version = 'v15.0';
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => '3118402164',
            'type' => 'template',
            "template" => [
                "name" => "hello_world",
                "language" => [
                    "code" => "en_US"
                ]
            ]
        ];

        $message = Http::withToken($token)->post('https://graph.facebook.com/' . $version . '/' . $phoneId . '/messages', $payload)->throw()->json();

        //     $wp = new Whatsapp();
        //     $message = $wp->sendText('14842918777', 'Is this working?');

        //     return response()->json([
        //         'success' => true,
        //         'data' => $message,
        //     ], 200);
        // } catch (Exception $e) {
        //     Log::error('Error al obtener mensajes4: ' . $e->getMessage());
        //     return response()->json([
        //         'success'  => false,
        //         'error' => $e->getMessage(),
        //     ], 500);
        // }
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
            Log::error('Error al obtener mensajes7: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
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

            foreach ($recipients as $recipient) {
                $phone = (int) filter_var($recipient, FILTER_SANITIZE_NUMBER_INT);
                $payload['to'] = $phone;

                SendMessage::dispatch($tokenApp, $phone_id, $payload, $body, $messageData);
            }

            return response()->json([
                'success' => true,
                'data' => count($recipients) . ' Mensajes encolado correctamente.',
            ], 200);
        } catch (Exception $e) {
            Log::error('Error al obtener mensajes8: ' . $e->getMessage());
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
