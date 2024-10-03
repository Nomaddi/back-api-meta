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



    public function destroy($id)
    {
        // Buscar el bot por ID
        $bot = Bot::findOrFail($id);

        // Verificar si el bot pertenece a una de las aplicaciones del usuario autenticado
        if (
            $bot->aplicaciones()->whereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })->exists()
        ) {
            // Eliminar el bot
            $bot->delete();

            return response()->json(['success' => 'Bot eliminado con éxito.']);
        } else {
            return response()->json([
                'error' => 'No tienes permiso para eliminar este bot.'
            ], 403);
        }
    }


    // moetodo para crear bot con asistente openai
    public function createBot(Request $request)
    {


        try {
            $request->validate([
                'nombre' => 'required',
                'descripcion' => 'required',
                'archivos' => 'required',
                'instrucciones' => 'required',
                'openai_key' => 'required',
                'openai_org' => 'required',
                'aplicacion_id' => 'required|exists:aplicaciones,id',
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
                        'file' => $rutaDestino . $nombreArchivo,
                        'purpose' => 'assistants',

                    ]);

                    dd($uploadedFile);
                    // $this->info('File ID: '.$uploadedFile->id);
                }
            }




            return response()->json([
                'success' => 'archivos guardado con exito.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al subir al guardar el archivo: ' . $e->getMessage()
            ], 500);
        }




        // bucle de los archivos recibidos, crear una carpeta con el nombre del bot y guardar los archivos, despues para los archivos aqui OpenAI::files()->upload(


        // $uploadedFile = OpenAI::files()->upload([
        //     'file' => Storage::disk('local')->readStream('bot'),
        //     'purpose' => 'assistants',
        // ]);








        // Obtener la aplicación
        $aplicacion = Aplicaciones::findOrFail($request->aplicacion_id);
        // $user = Auth::user();

        // Verificar si la aplicación ya tiene un bot asociado y desasociarlo
        // $botAnterior = $aplicacion->bot()->first();
        // if ($botAnterior) {
        //     $aplicacion->bot()->detach($botAnterior->id); // Desasociar el bot anterior si existe
        // }




        // Crear un nuevo bot
        // $bot = Bot::create([
        //     'user_id' => $user->id,
        //     'nombre' => $request->nombre,
        //     'descripcion' => $request->descripcion,
        //     'openai_key' => $request->openai_key,
        //     'openai_org' => $request->openai_org,
        //     'openai_assistant' => ,
        // ]);

    }

}
