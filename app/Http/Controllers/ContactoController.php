<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Contacto;
use App\Models\Tag;
use App\Imports\ContactosImport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;

class ContactoController extends Controller
{
    public function index()
    {
        $tags = Tag::all(); // O cualquier lógica que uses para obtener las etiquetas
        return view('contactos.index', compact('tags'));
    }

    public function getData()
    {
        $query = Contacto::with('tags');
        return DataTables::of($query)
            ->addColumn('tags', function ($contacto) {
                return $contacto->tags->map(function ($tag) {
                    return '<span style="background-color: ' . $tag->color . '; padding: 5px; border-radius: 4px;">' . $tag->nombre . '</span>';
                })->implode(' ');
            })
            ->addColumn('actions', 'contactos.datatables.actions')
            ->rawColumns(['tags', 'actions'])
            ->toJson();
    }

    // public function index()
    // {
    //     $contactos = Contacto::with('tags')->paginate(20);
    //     $tags = Tag::all();
    //     return view('contactos/index', [
    //         'contactos' => $contactos,
    //         'tags' => $tags
    //     ]);
    // }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
            'notas' => 'required',
            'etiqueta' => 'required|array',
        ]);
        // $contacto = (new Contacto)->createWithTags([
        //     'nombre' => $request->nombre,
        //     'apellido' => $request->apellido,
        //     'correo' => $request->correo,
        //     'telefono' => $request->telefono,
        //     "notas"     => $request->notas,

        //     // Otros campos...
        //     'tags' => $request->etiqueta, // Ajusta esto según la estructura de tu CSV
        // ]);

        $contacto = new Contacto();
        $contacto->nombre = $request->nombre;
        $contacto->apellido = $request->apellido;
        $contacto->correo = $request->correo;
        $contacto->telefono = $request->telefono;
        $contacto->notas = $request->notas;
        $contacto->save();

        $tag = Tag::whereIn('id', $request->etiqueta)->get();

        if ($tag->count() > 0) {
            $contacto->tags()->attach($tag);
        }

        return response()->json(['success' => 'Numero creado con éxito.']);


    }

    public function update(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'apellido' => 'required',
            'correo' => 'required',
            'telefono' => 'required',
            'notas' => 'required',
            'etiqueta' => 'required|array', // Asegúrate de que 'etiqueta' sea un array
        ]);
        try {
            $contacto = Contacto::findOrFail($request->id);
            $contacto->nombre = $request->nombre;
            $contacto->apellido = $request->apellido;
            $contacto->correo = $request->correo;
            $contacto->telefono = $request->telefono;
            $contacto->notas = $request->notas;
            $contacto->save();

            $tagIds = $request->etiqueta; // Obtén un array de IDs de etiquetas a asignar al contacto

            $contacto->tags()->sync($tagIds);

            return response()->json([
                'success' => true,
                'data' => $contacto,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $contacto = Contacto::findOrFail($request->id);

        // Elimina las relaciones de muchos a muchos con las etiquetas
        $contacto->tags()->detach();

        // Luego elimina el contacto
        $contacto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contacto eliminado correctamente.',
        ], 200);
    }

    public function uploadUsers(Request $request)
    {
        try {
            Excel::import(new ContactosImport, $request->file);
            return response()->json([
                'success' => true,
                'message' => 'Importación correcta.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la importación: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Añade el trace para obtener más detalles
            ], 500);
        }
    }
}
