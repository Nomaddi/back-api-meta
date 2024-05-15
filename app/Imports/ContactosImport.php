<?php

namespace App\Imports;

use App\Models\Tag;
use App\Models\Contacto;
use App\Models\UserContact;
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
        $contacto = Contacto::where('telefono', $row['telefono'])->first();

        if ($contacto) {
            // Si el contacto ya existe, verificar si el usuario actual ya lo tiene asociado
            if (!$user->contactos->contains($contacto->id)) {
                // Asociar el contacto existente con el usuario actual en user_contacts
                $userContact = new UserContact();
                $userContact->user_id = $user->id;
                $userContact->contacto_id = $contacto->id;
                $userContact->save();
            }
        } else {
            // Si no existe, crear un nuevo contacto
            $contacto = Contacto::create([
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'correo' => $row['correo'],
                'telefono' => $row['telefono'],
                "notas" => $row['notas'],
            ]);

            // Asociar el nuevo contacto con el usuario autenticado en user_contacts
            $userContact = new UserContact();
            $userContact->user_id = $user->id;
            $userContact->contacto_id = $contacto->id;
            $userContact->save();
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

            // Asociar los tags al contacto
            $contacto->tags()->syncWithoutDetaching($tagIds);
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
                'integer',
                // 'digits:12',
                'required'
            ],
            '*.tags' => [
                'required'
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            '*.nombre.required' => 'El campo nombre es obligatorio.',
            '*.nombre.max' => 'El campo nombre no debe superar los 255 caracteres.',
            '*.telefono.integer' => 'El campo teléfono debe ser un número entero.',
            // '*.telefono.max' => 'El campo teléfono no debe superar los 12 dígitos.',
            '*.telefono.required' => 'El campo teléfono es obligatorio.',
            '*.tags.required' => 'El campo tags es obligatorio.',
        ];
    }
}
