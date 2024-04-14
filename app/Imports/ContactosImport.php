<?php

namespace App\Imports;

use App\Models\Tag;
use App\Models\Contacto;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithValidation;

class  ContactosImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    // , WithBatchInserts, WithChunkReading
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $contacto = Contacto::create([
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'correo' => $row['correo'],
            'telefono' => $row['telefono'],
            "notas" => $row['notas'],
        ]);

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
            $contacto->tags()->sync($tagIds);
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
                'digits:12',
                'unique:contactos',
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
            '*.telefono.max' => 'El campo teléfono no debe superar los 12 dígitos.',
            '*.telefono.required' => 'El campo teléfono es obligatorio.',
            '*.tags.required' => 'El campo tags es obligatorio.',
        ];
    }
}
