<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use OpenAI\Factory;
use App\Models\User;
use App\Models\Thread;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use OpenAI\Responses\Threads\Runs\ThreadRunResponse;

class BotIA extends Controller
{
    // No utilizamos los tipos aquí para versiones anteriores a PHP 7.4
    public $question;
    public $answer;
    public $error;

    public function __construct()
    {
        $this->question = null; // Asignar valores predeterminados manualmente
        $this->answer = null;
        $this->error = null;
    }


    // Método para manejar preguntas

    public function askBot(Request $request)
    {
        $botId = $request->input('botId');  // Obtener el botId enviado desde el frontend
        if ($botId) {
            // obtener el bot desde la base de datos
            $bot = Bot::find($botId);
            // obterner user autenticado
            $user = Auth::user();


            $question = $request->input('question');
            $waId = $user->phone;  // Obtener el waId del usuario autenticado
            $openai_key = $bot->openai_key;  // Usar el API Key desde el .env
            $openai_org = $bot->openai_org;  // Usar la organización desde el .env
            $openai_assistant = $bot->openai_assistant;  // Usar el assistant ID desde el .env

            // Llamar a la función ask para obtener la respuesta del bot
            $botResponse = $this->ask($question, $waId, $botId, $openai_key, $openai_org, $openai_assistant);

            return response()->json([
                'answer' => $botResponse,  // Devolver la respuesta del bot en formato JSON
            ]);
        }

    }

    public function ask($question, $waId, $botId, $openai_key, $openai_org, $openai_assistant)
    {
        $this->question = $question;

        // Buscar si ya existe un hilo para este usuario y bot específico
        $thread = Thread::where('wa_id', $waId)
            ->where('bot_id', $botId)
            ->first();

        if ($thread) {
            // Si existe un hilo, usar el hilo existente
            $threadRun = $this->continueThread($thread->thread_id, $openai_key, $openai_org, $openai_assistant);
        } else {
            // Si no existe un hilo, crear uno nuevo
            $threadRun = $this->createAndRunThread($openai_key, $openai_org, $openai_assistant);

            // Guardar el nuevo hilo en la base de datos asociado al bot
            Thread::create([
                'wa_id' => $waId,
                'thread_id' => $threadRun->threadId,
                'bot_id' => $botId,  // Relacionar el hilo con el bot correcto
            ]);
        }

        // Cargar la respuesta del hilo
        $this->loadAnswer($threadRun, $openai_key, $openai_org, $openai_assistant);

        return $this->answer;
    }



    // Método para crear y ejecutar un nuevo hilo
    // Método para crear y ejecutar un nuevo hilo
    private function createAndRunThread($openai_key, $openai_org, $openai_assistant)
    {
        $openAI = (new Factory())
            ->withApiKey($openai_key)
            ->withOrganization($openai_org)
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->make();

        return $openAI->threads()->createAndRun([
            'assistant_id' => $openai_assistant,
            'thread' => [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $this->question,
                    ],
                ],
            ],
        ]);
    }

    // Método para continuar un hilo existente
    private function continueThread($threadId, $openai_key, $openai_org, $openai_assistant)
    {
        $openAI = (new Factory())
            ->withApiKey($openai_key)
            ->withOrganization($openai_org)
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->make();

        $openAI->threads()->messages()->create(
            $threadId,
            [
                'role' => 'user',
                'content' => $this->question,
            ]
        );

        return $openAI->threads()->runs()->create(
            $threadId,
            [
                'assistant_id' => $openai_assistant,
            ]
        );
    }


    // Método para cargar la respuesta desde el hilo
    private function loadAnswer($threadRun, $openai_key, $openai_org, $openai_assistant)
    {
        $openAI = (new Factory())
            ->withApiKey($openai_key)
            ->withOrganization($openai_org)
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->make();

        while (in_array($threadRun->status, ['queued', 'in_progress'])) {
            $threadRun = $openAI->threads()->runs()->retrieve(
                $threadRun->threadId,
                $threadRun->id,
            );
        }

        if ($threadRun->status !== 'completed') {
            $this->error = 'Request failed, please try again';
            return;
        }

        $messageList = $openAI->threads()->messages()->list(
            $threadRun->threadId,
        );

        $this->answer = $messageList->data[0]->content[0]->text->value ?? 'No answer received';
    }

    public function askBotForEmbed(Request $request)
    {
        try {
            $botId = $request->input('botId');
            if ($botId) {
                // obtener el bot desde la base de datos
                $bot = Bot::find($botId);
                $question = $request->input('question');
                $waId = 3105326598;  // Obtener el waId del usuario autenticado
                $openai_key = $bot->openai_key;  // Usar el API Key desde el .env
                $openai_org = $bot->openai_org;  // Usar la organización desde el .env
                $openai_assistant = $bot->openai_assistant;  // Usar el assistant ID desde el .env
                // Lógica para procesar la solicitud y obtener la respuesta del bot
                $botResponse = $this->ask($question, $waId, $botId, $openai_key, $openai_org, $openai_assistant);
            }


            return response()->json(['answer' => $botResponse]);
        } catch (\Exception $e) {
            // Registra el error para depuración
            \Log::error('Error en askBotForEmbed: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno en el servidor'], 500);
        }
    }


}
