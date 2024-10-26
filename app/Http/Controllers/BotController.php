<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use OpenAI\Factory;
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


    // metodo editar bot
    public function edit($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Usuario no autenticado'
            ], 401); // 401 Unauthorized
        }

        $bot = Bot::findOrFail($id);

        // Obtener la primera aplicación asociada (si existe)
        $aplicacion_id = $bot->aplicaciones->isNotEmpty() ? $bot->aplicaciones->first()->id : null;

        return response()->json([
            'success' => 'Bot recuperado con éxito.',
            'data' => [
                'id' => $bot->id,
                'nombre' => $bot->nombre,
                'descripcion' => $bot->descripcion,
                'openai_key' => $bot->openai_key,
                'openai_org' => $bot->openai_org,
                'openai_assistant' => $bot->openai_assistant,
                'aplicacion_id' => $aplicacion_id // Incluir aplicacion_id en la respuesta
            ]
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

        $openAI = (new Factory())
            ->withApiKey($bot->openai_key)
            ->withOrganization($bot->openai_org)
            ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
            ->make();

        $openAI->assistants()->modify($bot->openai_assistant, [
            'name' => $request->nombre,
            'instructions' => $request->instructions,
            'model' => $request->model,
            'temperature' => floatval($request->temperature), // Convertir a decimal
            'top_p' => floatval($request->top_p), // Convertir a decimal


        ]);

        return response()->json([
            'success' => 'Bot actualizado con éxito.',
            'data' => $bot
        ]);
    }



    public function destroy($id)
    {
        try {
            // Buscar el bot por ID
            $bot = Bot::findOrFail($id);

            // Verificar si el bot pertenece a una de las aplicaciones del usuario autenticado
            if (
                // verificar si este bot le pernece al usuario autenticado
                Auth::user()->bots->contains($bot)
            ) {
                // Cambiar la clave API en tiempo de ejecución a la nueva clave necesaria
                $openAI = (new Factory())
                    ->withApiKey($bot->openai_key)
                    ->withOrganization($bot->openai_org)
                    ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
                    ->make();

                $response = $openAI->assistants()->delete($bot->openai_assistant);

                // Verificar si se eliminó con éxito en OpenAI
                if ($response->deleted) {
                    // Eliminar el bot de la base de datos
                    $bot->delete();

                    return response()->json([
                        'success' => 'Bot eliminado con éxito.',
                        'data' => $response->toArray()
                    ]);
                } else {
                    return response()->json([
                        'error' => 'No se pudo eliminar el bot en OpenAI.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'error' => 'No tienes permiso para eliminar este bot.'
                ], 403);
            }
        } catch (\OpenAI\Exceptions\ErrorException $e) {
            // Capturar errores específicos de OpenAI y devolver el mensaje al frontend
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Capturar otros errores generales
            return response()->json([
                'error' => 'Ocurrió un error al intentar eliminar el bot.'
            ], 500);
        }
    }



    // moetodo para crear bot con asistente openai
    public function createBot(Request $request)
    {


        try {

            $uploadedFile = null;
            $fileIds = []; // Inicializamos la lista de IDs de los archivos subidos

            $request->validate([
                'nombre' => 'required',
                'descripcion' => 'required',
                'archivos' => 'array',
                'archivos.*' => 'mimetypes:text/x-c,text/x-c++,text/x-csharp,text/css,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/x-golang,text/html,text/x-java,text/javascript,application/json,text/markdown,application/pdf,text/x-php,application/vnd.openxmlformats-officedocument.presentationml.presentation,text/x-python,text/x-script.python,text/x-ruby,application/x-sh,text/x-tex,application/typescript,text/plain',
                'openai_key' => 'required',
                'openai_org' => 'required',
                'instrucciones' => 'required',
                'modelo' => 'required',
                'temperature' => 'required|numeric',
                'top_p' => 'required|numeric',
                'aplicacion_id' => 'nullable|exists:aplicaciones,id',
            ]);

            $nombreCarpeta = $request->nombre;

            // Crear una instancia personalizada de OpenAI con las credenciales del usuario y el encabezado requerido
            $openAI = (new Factory())
                ->withApiKey($request->openai_key)
                ->withOrganization($request->openai_org)
                ->withHttpHeader('OpenAI-Beta', 'assistants=v2') // Agregar el encabezado necesario
                ->make();

            // Procesar los archivos
            if ($request->hasfile('archivos')) {
                foreach ($request->file('archivos') as $archivo) {
                    // Define la carpeta de destino
                    $rutaDestino = 'uploads/' . $nombreCarpeta . '/'; // Puedes cambiar esto a la ruta que prefieras

                    // Crear un nombre único para cada archivo
                    $nombreArchivo = time() . '-' . $archivo->getClientOriginalName();

                    // Mover el archivo a la carpeta especificada
                    $archivo->move(public_path($rutaDestino), $nombreArchivo);

                    $uploadedFile = $openAI->files()->upload([
                        'file' => fopen($rutaDestino . $nombreArchivo, 'r'),  // Abre el archivo como un stream
                        'purpose' => 'assistants',
                    ]);

                    // Agregar el ID del archivo subido a la lista de file_ids
                    $fileIds[] = $uploadedFile->id;


                }
                //creacion de vector store
                $vector = $openAI->vectorStores()->create([
                    'file_ids' => $fileIds,
                    'name' => 'vector-store-' . $nombreCarpeta,
                ]);

                // Crear el asistente utilizando todos los IDs de los archivos subidos
                $assistant = $openAI->assistants()->create([
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
                    'model' => $request->modelo,
                    'temperature' => floatval($request->temperature), // Convertir a decimal
                    'top_p' => floatval($request->top_p), // Convertir a decimal

                ]);
            }

            // Crear el asistente utilizando todos los IDs de los archivos subidos
            $assistant = $openAI->assistants()->create([
                'name' => $request->nombre,
                'instructions' => $request->instrucciones,
                'model' => $request->modelo,
                'temperature' => floatval($request->temperature), // Convertir a decimal
                'top_p' => floatval($request->top_p), // Convertir a decimal

            ]);

            // Verifica que $assistant se haya creado correctamente antes de asignar openai_assistant
            if (!$assistant || !isset($assistant->id)) {
                return response()->json(['error' => 'No se pudo crear el asistente de OpenAI.'], 400);
            }

            // Obtener la aplicación
            $user = Auth::user();
            // Crear un nuevo bot
            $bot = Bot::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'openai_key' => $request->openai_key,
                'openai_org' => $request->openai_org,
                'openai_assistant' => $assistant->id,
            ]);

            // Asociar el bot con la aplicación solo si se seleccionó una aplicación
            if ($request->filled('aplicacion_id')) {
                $aplicacion = Aplicaciones::findOrFail($request->aplicacion_id);
                $botAnterior = $aplicacion->bot()->first();
                if ($botAnterior) {
                    $aplicacion->bot()->detach($botAnterior->id);
                }
                $aplicacion->bot()->attach($bot->id);
            }

            return response()->json([
                'success' => 'Bot creado con éxito y asociado a la aplicación.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al guardar asistente: ' . $e->getMessage()
            ], 500);
        }

    }


    // consultar asistente de openai
    public function BotOpenai($id)
    {
        $bot = Bot::findOrFail($id);

        // Verificar si el bot pertenece al usuario autenticado
        if (Auth::user()->bots->contains($bot)) {
            $openAI = (new Factory())
                ->withApiKey($bot->openai_key)
                ->withOrganization($bot->openai_org)
                ->withHttpHeader('OpenAI-Beta', 'assistants=v2') // Agregar el encabezado necesario
                ->make();
            $data = $openAI->assistants()->retrieve($bot->openai_assistant);

            return response()->json([
                'success' => 'Asistente recuperado con éxito.',
                'data' => $data
            ]);
        } else {
            return response()->json([
                'error' => 'El usuario no tiene acceso a este asistente.'
            ], 403);
        }
    }



}
