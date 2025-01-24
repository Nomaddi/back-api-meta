<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\UserEmail;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class GroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::query();

        // Verifica si hay un término de búsqueda
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Pagina los resultados
        $groups = $query->withCount('userEmails')->paginate(10);

        return view('groups.index', compact('groups'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $group = Group::create($validated);
        return redirect()->back()->with('success', 'Grupo creado correctamente.');
    }

    public function create()
    {
        return view('groups.create');
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id); // Busca el grupo por ID o lanza un error 404
        return view('groups.edit', compact('group')); // Retorna la vista para editar
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $group = Group::findOrFail($id);
        $group->update($validated);

        return redirect()->route('groups.index')->with('success', 'Grupo actualizado correctamente.');
    }

    public function showEmails($id, Request $request)
    {
        $group = Group::findOrFail($id);

        // Filtrar por búsqueda si se envía una consulta
        $query = $group->userEmails();

        if ($request->has('search') && $request->search) {
            $query->where('email', 'like', '%' . $request->search . '%')
                ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        // Paginación (20 por página)
        $recipients = $query->paginate(20);

        return view('groups.emails', compact('group', 'recipients'));
    }



    public function addRecipient(Request $request, $groupId)
    {
        $group = Group::findOrFail($groupId);

        if ($request->method === 'individual') {
            // Validación y creación de un destinatario individual
            $validated = $request->validate([
                'name' => 'nullable|string|max:255',
                'email' => 'required|email|unique:user_emails,email',
            ]);

            $recipient = UserEmail::create($validated);
            $group->userEmails()->attach($recipient->id);

            return redirect()->back()->with('success', 'Destinatario agregado al grupo.');
        }

        if ($request->method === 'file') {
            // Validación del archivo CSV
            $request->validate([
                'file' => 'required|file|mimes:csv,txt',
            ]);

            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path)); // Convertir CSV en un array

            // Asume que la primera fila contiene los encabezados
            $headers = array_shift($data);

            $errors = []; // Para registrar errores por fila
            $importedCount = 0;

            foreach ($data as $index => $row) {
                $row = array_combine($headers, $row);

                // Validar cada fila
                $validator = Validator::make($row, [
                    'email' => 'required|email|unique:user_emails,email',
                    'name' => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    // Registrar el error con el número de fila
                    $errors[] = [
                        'row' => $index + 2, // +2 porque la primera fila es encabezado y las filas del CSV inician en 1
                        'errors' => $validator->errors()->all(),
                    ];
                    continue;
                }

                // Crear el destinatario y asociarlo al grupo
                $validated = $validator->validated();
                $recipient = UserEmail::create($validated);
                $group->userEmails()->attach($recipient->id);
                $importedCount++;
            }

            // Manejar el resultado de la importación
            if (count($errors) > 0) {
                $errorFile = 'errors_' . now()->timestamp . '.csv';
                $errorData = array_map(function ($error) {
                    return [
                        'Fila' => $error['row'],
                        'Error' => implode(', ', $error['errors']),
                    ];
                }, $errors);

                // Generar el archivo CSV
                $handle = fopen(storage_path("app/public/$errorFile"), 'w');
                fputcsv($handle, ['Fila', 'Errores']);
                foreach ($errorData as $line) {
                    fputcsv($handle, $line);
                }
                fclose($handle);

                return redirect()->back()
                    ->with('errors', $errors)
                    ->with('errorFile', "/storage/$errorFile")
                    ->with('success', "$importedCount destinatarios importados correctamente. Algunos registros tienen errores.");
            }

            return redirect()->back()->with('success', 'Todos los destinatarios fueron importados correctamente.');
        }

        return redirect()->back()->with('error', 'Método no válido.');
    }



    public function editRecipient($groupId, $recipientId)
    {
        $group = Group::findOrFail($groupId);
        $recipient = UserEmail::findOrFail($recipientId);

        return view('groups.editRecipient', compact('group', 'recipient'));
    }

    public function updateRecipient(Request $request, $groupId, $recipientId)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:user_emails,email,' . $recipientId,
        ]);

        $recipient = UserEmail::findOrFail($recipientId);
        $recipient->update($validated);

        return redirect()->route('groups.showEmails', $groupId)->with('success', 'Destinatario actualizado con éxito.');
    }

    public function removeRecipient($groupId, $recipientId)
    {
        $group = Group::findOrFail($groupId);
        $recipient = UserEmail::findOrFail($recipientId);

        // Elimina la relación entre el grupo y el destinatario
        $group->userEmails()->detach($recipient->id);

        // Si quieres eliminar también al destinatario del sistema completamente:
        $recipient->delete();

        return redirect()->back()->with('success', 'Destinatario eliminado del grupo.');
    }


}
