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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Contacto::with('tags');
            return DataTables::of($data)->addIndexColumn()
                ->addColumn('tags', function ($contacto) {
                    return $contacto->tags->map(function ($tag) {
                        return '<span style="background-color: ' . $tag->color . '; padding: 5px; border-radius: 4px;">' . $tag->nombre . '</span>';
                    })->implode(' ');
                })

                ->addColumn('action', function ($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id . '" class="edit btn btn-primary btn-sm" style="margin-right: 8px;"> <i class="fa fa-edit"></i></button>';
                    $button .= '<button type="button" name="edit" id="' . $data->id . '" class="delete btn btn-danger btn-sm"> <i class="fa fa-trash"></i></button>';
                    return $button;
                })

                ->rawColumns(['tags', 'action'])
                ->make(true);
        }

        $tags = Tag::all(); // O cualquier lógica que uses para obtener las etiquetas
        return view('contactos.index', compact('tags'));
    }

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

        return response()->json(['success' => 'Contacto creado con éxito.']);


    }

    public function edit($id)
    {
        if (request()->ajax()) {
            $data = Contacto::with('tags')
                ->findOrFail($id);
            return response()->json(['result' => $data]);
        }
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
        $contacto = Contacto::findOrFail($request->hidden_id);
        $contacto->nombre = $request->nombre;
        $contacto->apellido = $request->apellido;
        $contacto->correo = $request->correo;
        $contacto->telefono = $request->telefono;
        $contacto->notas = $request->notas;
        $contacto->save();

        $tagIds = $request->etiqueta ?? []; // Obtén un array de IDs de etiquetas a asignar al contacto

        $contacto->tags()->sync($tagIds);

        return response()->json(['success' => 'Contacto actualizado con exito']);
    }

    public function destroy($id)
    {
        $contacto = Contacto::findOrFail($id);

        // Elimina las relaciones de muchos a muchos con las etiquetas
        $contacto->tags()->detach();

        // Luego elimina el contacto
        $contacto->delete();
    }

    public function uploadUsers(Request $request)
    {
        try {
            Excel::import(new ContactosImport, $request->file);
            return response()->json([
                'success' => true,
                'message' => 'Importación correcta.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la importación: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(), // Añade el trace para obtener más detalles
            ], 500);
        }
    }
}
