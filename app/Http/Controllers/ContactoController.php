<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Throwable;
use App\Models\Contacto;
use App\Models\Tag;
use App\Imports\ContactosImport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use Illuminate\Validation\ValidationException;

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
        // Define las reglas de validación
        $request->validate([
            'nombre' => 'required',
            'correo' => 'email',
            'telefono' => 'required|unique:contactos', // Asegura que el teléfono sea único
            'etiqueta' => 'required|array',
        ]);

        try {
            // Crear el contacto si la validación pasa
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
        } catch (Exception $e) {
            // Captura y maneja cualquier excepción que ocurra durante la creación del contacto
            if ($e instanceof \Illuminate\Database\QueryException) {
                // Si es un error de duplicado (teléfono o correo electrónico)
                if ($e->errorInfo[1] == 1062) {
                    return response()->json(['error' => 'No se puede crear el contacto porque ya existe un registro con el mismo número de teléfono o correo electrónico.'], 400);
                }
            }

            // Otro tipo de errores
            return response()->json(['error' => 'Ha ocurrido un error al crear el contacto.'], 500);
        }
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
            'correo' => 'email',
            'telefono' => 'required',
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
            return redirect()->route('contactos.index')->with('success', 'Contactos importados con éxito');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Obtener los errores de validación del CSV
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Fila " . $failure->row() . ": " . $failure->errors()[0];
            }
            // Redireccionar de vuelta con los errores
            return redirect()->back()->withErrors($errors)->withInput();
        } catch (Exception $e) {
            // Otro tipo de errores
            return redirect()->back()->withErrors(['error' => 'Ha ocurrido un error al importar los contactos.'])->withInput();
        }
    }


    public function exportar()
    {
        $contactos = Contacto::with('tags')->get(); // Cargar anticipadamente las etiquetas
        $nombreArchivo = 'contactos.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$nombreArchivo",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columnas = array('ID', 'Nombre', 'Teléfono', 'Etiquetas'); // Agrega 'Etiquetas'

        $callback = function () use ($contactos, $columnas) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columnas);

            foreach ($contactos as $contacto) {
                // Concatenar todas las etiquetas en una cadena separada por comas
                $etiquetas = $contacto->tags->pluck('nombre')->implode(', ');

                fputcsv($file, array ($contacto->id, $contacto->nombre, $contacto->telefono, $etiquetas));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


}
