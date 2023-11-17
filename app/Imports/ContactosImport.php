<?php

namespace App\Imports;

use App\Models\Contacto;
use App\Models\Tag;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactosImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $contacto = (new Contacto)->createWithTags([
            'nombre' => $row['nombre'],
            'apellido' => $row['apellido'],
            'correo' => $row['correo'],
            'telefono' => $row['telefono'],
            "notas"     => $row['notas'],

            // Otros campos...
            'tags' => $row['tags'], // Ajusta esto según la estructura de tu CSV
        ]);
        
        return $contacto;
    }
}
