<?php

namespace App\Imports;

use App\Models\Tag;
use App\Models\Contacto;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ContactosImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function model(array $row)
    {
        $user = $this->user;

        /// Intentar encontrar un contacto existente con el mismo teléfono que aún no está asociado con este usuario
        $contactoExistente = Contacto::where('telefono', $row['telefono'])
            ->whereDoesntHave('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->first();

        if ($contactoExistente) {
            // Aquí deberías asociar el contacto existente con el usuario actual
            $user->contactos()->syncWithoutDetaching([$contactoExistente->id]);
            $contacto = $contactoExistente; // Asegúrate de asignar el contacto existente a la variable $contacto para el manejo de tags
        } else {
            // Crear un nuevo contacto si no existe
            $contacto = Contacto::create([
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'correo' => $row['correo'],
                'telefono' => $row['telefono'],
                "notas" => $row['notas'],
            ]);
        }

        // Si 'tags' está presente y no es nulo
        if (!empty($row['tags'])) {
            // Separar los nombres de tags y encontrar/crear los tags
            $tagNames = explode(',', $row['tags']);
            $tagIds = [];

            foreach ($tagNames as $tagName) {
                // Asegurarse de que no haya espacios extra alrededor del nombre del tag
                $tagNameTrimmed = trim($tagName);

                // Encontrar o crear el Tag basado en el nombre
                $tag = Tag::firstOrCreate(['nombre' => $tagNameTrimmed]);
                $tagIds[] = $tag->id;
            }

            // Asociar los tags al contacto sin eliminar relaciones previas de tags
            $contacto->tags()->syncWithoutDetaching($tagIds);

            // NOTA: Asegúrate de que esta parte del código es necesaria
            // Asociar los tags al usuario sin eliminar previas relaciones de tags
            if ($user) {
                $user->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }

    public function batchSize(): int
    {
        return 4000;
    }

    public function chunkSize(): int
    {
        return 4000;
    }


    public function rules(): array
    {
        return [
            '*.nombre' => [
                'max:255',
                'required'
            ],
            '*.telefono' => [
                'required',
                'integer',
                'digits:12',
                function ($attribute, $value, $fail) {
                    if ($this->contactExistsForCurrentUser($value)) {
                        $fail('El número de teléfono ya está registrado para este usuario.');
                    }
                },
            ],
            '*.tags' => [
                'required',
                function ($attribute, $value, $fail) {
                    $tags = explode(',', $value);
                    foreach ($tags as $tag) {
                        $tag = trim($tag);
                        if (!$this->tagExistsForCurrentUser($tag)) {
                            $fail("El tag, $tag no existe o no está asociado con su cuenta.");
                        }
                    }
                },
            ],
        ];
    }

    protected function contactExistsForCurrentUser($phone)
    {
        // Comprobar si el usuario actual ya tiene un contacto con este número de teléfono
        return $this->user->contactos()->where('telefono', $phone)->exists();
    }

    protected function tagExistsForCurrentUser($tagName)
    {

        return $this->user->tags()->where('nombre', $tagName)->exists();
    }

    public function customValidationMessages()
    {
        return [
            '*.nombre.required' => 'El campo nombre es obligatorio.',
            '*.nombre.max' => 'El campo nombre no debe superar los 255 caracteres.',
            '*.telefono.integer' => 'El campo teléfono debe ser un número entero.',
            '*.telefono.max' => 'El campo teléfono no debe superar los 12 dígitos.',
            '*.telefono.required' => 'El campo teléfono es obligatorio.',
            '*.tags.required' => 'El campo tags es obligatorio.',
        ];
    }
}
