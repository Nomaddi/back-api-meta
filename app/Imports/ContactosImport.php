<?php

namespace App\Imports;

use App\Models\Tag;
use App\Models\Contacto;
use App\Models\CustomField;
use App\Models\UserContact;
use App\Models\CustomFieldValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ContactosImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
{
    protected $user;
    protected $customFieldMap;

    public function __construct()
    {
        $this->user = Auth::user();
        // Crear un mapa de los nombres de los campos personalizados a sus IDs
        $this->customFieldMap = CustomField::where('user_id', $this->user->id)->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [$this->normalizeName($key) => $item];
        });
    }

    // Método para normalizar los nombres (reemplaza espacios por guiones bajos y pasa a minúsculas)
    protected function normalizeName($name)
    {
        return str_replace(' ', '_', strtolower($name));
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'UTF-8'
        ];
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

        // Manejar campos personalizados si están presentes
        foreach ($row as $key => $value) {
            $normalizedKey = $this->normalizeName($key); // Normalizar el nombre del campo

            if (isset($this->customFieldMap[$normalizedKey])) {
                $customFieldId = $this->customFieldMap[$normalizedKey];

                // Verificar si el valor está presente
                if (!is_null($value) && $value !== '') {
                    Log::info('Updating Custom Field:', ['contacto_id' => $contacto->id, 'custom_field_id' => $customFieldId, 'value' => $value]); // Agrega esta línea para depuración

                    $customFieldValue = CustomFieldValue::updateOrCreate(
                        ['contacto_id' => $contacto->id, 'custom_field_id' => $customFieldId],
                        ['value' => $value]
                    );

                    // Verificar si el registro se creó o actualizó correctamente
                    if ($customFieldValue->wasRecentlyCreated || $customFieldValue->wasChanged()) {
                        Log::info('Custom Field Value Created/Updated Successfully:', $customFieldValue->toArray());
                    } else {
                        Log::error('Failed to Create/Update Custom Field Value:', ['contacto_id' => $contacto->id, 'custom_field_id' => $customFieldId]);
                    }
                } else {
                    Log::info('Skipping Custom Field Update due to empty value:', ['contacto_id' => $contacto->id, 'custom_field_id' => $customFieldId, 'key' => $key]);
                }
            } else {
                Log::warning('Custom Field Key Not Found in Map:', ['key' => $key, 'normalizedKey' => $normalizedKey]);
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
