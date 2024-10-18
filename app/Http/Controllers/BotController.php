<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\Aplicaciones;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Auth;

class BotController extends Controller
{

    public function index(Request $request)
    {
        // Obtener el usuario logueado
        $user = Auth::user();

        if ($user) {
            // Obtener todas las aplicaciones del usuario con sus bots asociados
            $aplicaciones = $user->aplicaciones()->with('bot')->get();
            // Obtener todos los bots (independientemente de si están asociados a una aplicación)
            $todosLosBots = $user->bots;
            // obtener todas las aplicaciones
            $aplicaciones2 = Aplicaciones::all();
        } else {
            return redirect('login')->with('error', 'Debe estar logueado para ver las aplicaciones.');
        }

        return view('bots/index', [
            'aplicaciones' => $aplicaciones,
            'todosLosBots' => $todosLosBots,
            'aplicaciones2' => $aplicaciones2,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'openai_key' => 'required',
            'openai_org' => 'required',
            'openai_assistant' => 'required',
            'aplicacion_id' => 'required|exists:aplicaciones,id',
        ]);

        // Obtener la aplicación
        $aplicacion = Aplicaciones::findOrFail($request->aplicacion_id);
        $user = Auth::user();

        // Verificar si la aplicación ya tiene un bot asociado y desasociarlo
        $botAnterior = $aplicacion->bot()->first();
        if ($botAnterior) {
            $aplicacion->bot()->detach($botAnterior->id); // Desasociar el bot anterior si existe
        }

        // Crear un nuevo bot
        $bot = Bot::create([
            'user_id' => $user->id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'openai_key' => $request->openai_key,
            'openai_org' => $request->openai_org,
            'openai_assistant' => $request->openai_assistant,
        ]);

        // Asociar el nuevo bot con la aplicación en la tabla pivote
        $aplicacion->bot()->attach($bot->id);

        return response()->json([
            'success' => 'Bot creado con éxito y asociado a la aplicación.',
            'data' => $bot
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'descripcion' => 'required',
            'openai_key' => 'required',
            'openai_org' => 'required',
            'openai_assistant' => 'required',
            'aplicacion_id' => 'required|exists:aplicaciones,id' // Validar que la aplicación existe
        ]);

        

        $bot = Bot::findOrFail($id);

        // Desasociar la aplicación seleccionada de cualquier otro bot
        $aplicacion = Aplicaciones::findOrFail($request->aplicacion_id);

        // Buscar si esta aplicación ya está asociada a otro bot
        $botAnterior = $aplicacion->bot()->first();
        if ($botAnterior && $botAnterior->id !== $bot->id) {
            // Desasociar la aplicación del bot anterior
            $aplicacion->bot()->detach($botAnterior->id);
        }

        // Actualizar los datos del bot
        $bot->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'openai_key' => $request->openai_key,
            'openai_org' => $request->openai_org,
            'openai_assistant' => $request->openai_assistant,
        ]);

        // Asociar la aplicación seleccionada al bot actual
        $aplicacion->bot()->sync([$bot->id]);

        return response()->json([
            'success' => 'Bot actualizado con éxito.',
            'data' => $bot
        ]);
    }

//Si el usuario autenticado es el propietario del bot (es decir, el user_id del bot coincide con el id del usuario autenticado), 
// el bot puede ser eliminado, incluso si no tiene aplicaciones asociadas.

    public function destroy($id)
    {
    // Buscar el bot por ID
    $bot = Bot::findOrFail($id);
    
    // Registro para verificar que el bot fue encontrado
    \Log::info('Bot encontrado: ' . $bot->id);

    // Obtener el usuario autenticado
    $userId = Auth::id();
    \Log::info('Usuario autenticado: ' . $userId);

    // Ver todas las aplicaciones asociadas con el bot
    $aplicacionesBot = $bot->aplicaciones;
    \Log::info('Aplicaciones asociadas al bot: ' . $aplicacionesBot->pluck('id'));

    // Verificar si el usuario es el propietario del bot (independientemente de las aplicaciones)
    if ($bot->user_id === $userId) {
        // Eliminar el bot
        $bot->delete();

        \Log::info('Bot eliminado con éxito: ' . $bot->id);

        return response()->json(['success' => 'Bot eliminado con éxito.']);
    }

    // Verificar si el bot pertenece a una de las aplicaciones del usuario autenticado
    $aplicacionesRelacionadas = $bot->aplicaciones()->whereHas('users', function ($query) use ($userId) {
        $query->where('user_id', $userId);
        })->get();

        // Registro de aplicaciones relacionadas
        \Log::info('Aplicaciones relacionadas con el usuario autenticado: ' . $aplicacionesRelacionadas->pluck('id'));

        // Verificar si hay aplicaciones relacionadas con el usuario
        if ($aplicacionesRelacionadas->isNotEmpty()) {
            // Eliminar el bot
            $bot->delete();

            \Log::info('Bot eliminado con éxito: ' . $bot->id);
            return response()->json(['success' => 'Bot eliminado con éxito.']);
            } else {
                // Registro para depurar la falta de permiso
                \Log::warning('No tienes permiso para eliminar este bot. Bot ID: ' . $bot->id . ' | User ID: ' . $userId);

            return response()->json([
            'error' => 'No tienes permiso para eliminar este bot.'
        ], 403);
    }
}


    // metodo para crear bot con asistente openai
    public function createBot(Request $request)
    {


        try {

            $uploadedFile = null;
            $fileIds = []; // Inicializamos la lista de IDs de los archivos subidos

            $request->validate([
                'nombre' => 'required',
                'descripcion' => 'required',
                'archivos' => 'required',
                'instrucciones' => 'required',
                'openai_key' => 'required',
                'openai_org' => 'required',
                'aplicacion_id' => 'required|exists:aplicaciones,id',
            ]);
            $data = OpenAI::assistants()->list([
                'limit' => 10,
            ]);

            $nombreCarpeta = $request->nombre;

            // Procesar los archivos
            if ($request->hasfile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    // Define la carpeta de destino
                    $rutaDestino = 'uploads/' . $nombreCarpeta . '/'; // Puedes cambiar esto a la ruta que prefieras

                    // Crear un nombre único para cada archivo
                    $nombreArchivo = time() . '-' . $archivo->getClientOriginalName();

                    // Mover el archivo a la carpeta especificada
                    $archivo->move(public_path($rutaDestino), $nombreArchivo);

                    $uploadedFile = OpenAI::files()->upload([
                        'file' => fopen($rutaDestino . $nombreArchivo, 'r'),  // Abre el archivo como un stream
                        'purpose' => 'assistants',
                    ]);

                    // Agregar el ID del archivo subido a la lista de file_ids
                    $fileIds[] = $uploadedFile->id;


                }
            }
            //creacion de vector store
            $vector = OPENAI::vectorStores()->create([
                'file_ids' => $fileIds,
                'name' => 'My first Vector Store',
            ]);

            // Crear el asistente utilizando todos los IDs de los archivos subidos
            $assistant = OpenAI::assistants()->create([
                'name' => $request->nombre,
                'tools' => [
                    [
                        'type' => 'file_search',
                    ],
                ],
                'tool_resources' => [
                    'file_search' => [
                        'vector_store_ids' => [$vector->id],
                    ],
                ],
                'instructions' => $request->instrucciones,
                'model' => 'gpt-4-1106-preview',
            ]);

            // Obtener la aplicación
            $aplicacion = Aplicaciones::findOrFail($request->aplicacion_id);
            $user = Auth::user();

            // Verificar si la aplicación ya tiene un bot asociado y desasociarlo
            $botAnterior = $aplicacion->bot()->first();
            if ($botAnterior) {
                $aplicacion->bot()->detach($botAnterior->id); // Desasociar el bot anterior si existe
            }

            // Crear un nuevo bot
            $bot = Bot::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'openai_key' => $request->openai_key,
                'openai_org' => $request->openai_org,
                'openai_assistant' => $assistant->id,
            ]);


            // Asociar el nuevo bot con la aplicación en la tabla pivote
            $aplicacion->bot()->attach($bot->id);

            return response()->json([
                'success' => 'Bot creado con éxito y asociado a la aplicación.',
                'data' => $vector
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al guardar asistente: ' . $e->getMessage()
            ], 500);
        }

    }


    public function BotOpenai($id)
    {
        try {
            // Intenta hacer la solicitud a OpenAI
            $data = OpenAI::assistants()->retrieve($id);
    
            return response()->json([
                'success' => 'Asistente recuperado con éxito.',
                'data' => $data
            ]);
    
        } catch (\Exception $e) {
            // Captura cualquier error y lo registra en los logs de Laravel
            \Log::error('Error al recuperar la información del asistente: ' . $e->getMessage());
            
            // Retorna una respuesta con el error
            return response()->json([
                'error' => 'No se pudo recuperar la información del asistente.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    


    public function getAssistantInfo($assistantId)
    {
        $client = new Client(env('OPENAI_API_KEY'));
    
        try {
            // Recupera la información del asistente usando la API de OpenAI
            $response = $client->models()->retrieve($assistantId);
    
            // Convierte la respuesta en array
            $responseArray = $response->toArray();
    
            // Pasar la información a una vista
            return view('bots.assistant-info', [
                'modelDetails' => $responseArray
            ]);
            
        } catch (\Exception $e) {
            // Maneja cualquier error y redirige a la vista con un error
            return redirect()->back()->with('error', 'No se pudo recuperar la información del asistente.');
        }
    }
    

    public function showAssistant($botId)
{
    // Encuentra el bot por su ID
    $bot = Bot::find($botId);

    // Si no se encuentra el bot, redirige con un mensaje de error
    if (!$bot) {
        return redirect()->back()->with('error', 'Bot no encontrado.');
    }

    // Llamada a la API de OpenAI para obtener detalles relacionados con OPENAI_API_KEY
    try {
        // Recuperar el asistente usando el ID del asistente que ya está asociado al bot
        $assistantResponse = OpenAI::assistants()->retrieve($bot->openai_assistant);

        // Convertir la respuesta en un array para pasarlo a la vista
        $assistantData = $assistantResponse->toArray();

        return view('bots.assistant-info', [
            'bot' => $bot,
            'assistantData' => $assistantData, // Pasar los detalles del asistente
        ]);

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'No se pudo obtener la información de OpenAI.');
    }
}    

}
