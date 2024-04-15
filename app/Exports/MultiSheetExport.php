<?php

namespace App\Exports;

use App\Models\Message;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\Log;

class MultiSheetExport implements WithMultipleSheets
{
    use Exportable;

    protected $messages;
    protected $plantillas;

    public function __construct($messages, $plantillas)
    {
        $this->messages = $messages;
        $this->plantillas = $plantillas;
        Log::info('Exportación inicializada.');
    }

    public function sheets(): array
    {
        $sheets = [];

        // Hoja de cálculo para los mensajes
        $sheets[] = new MessagesSheet($this->messages);

        // Hoja de cálculo para las plantillas
        $sheets[] = new PlantillasSheet($this->plantillas);

        // Hoja de cálculo para estados
        $sheets[] = new StatusSheet($this->messages);

        return $sheets;
    }
}

class MessagesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $messages;

    public function __construct($messages)
    {
        $this->messages = $messages;
        Log::info('Procesando hoja de mensajes.');
    }

    public function collection()
    {
        return $this->messages->map(function ($message) {
            try {
                return [
                    $message->contacto->nombre,
                    $message->contacto->telefono,
                    $message->contacto->tags->pluck('nombre')->implode(', '),
                    $message->body,
                    $message->status,
                    $message->created_at,
                ];
            } catch (\Exception $e) {
                Log::error("Error procesando mensaje: {$e->getMessage()}");
                throw $e;
            }
        });
    }

    public function headings(): array
    {
        return ['Nombre', 'Teléfono', 'Etiquetas', 'Mensaje', 'Estado', 'Fecha'];
    }

    public function title(): string
    {
        return 'Mensajes';
    }
}

class PlantillasSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $plantillas;

    public function __construct($plantillas)
    {
        $this->plantillas = $plantillas;
        Log::info('Procesando hoja de plantillas.');
    }

    public function collection()
    {
        $resultado = [];

        foreach ($this->plantillas as $plantilla) {
            try {
                $ciudad = $this->extraerCiudad($plantilla->body);
                $clave = $plantilla->nombrePlantilla . '|' . $ciudad;

                if (!isset($resultado[$clave])) {
                    $resultado[$clave] = [
                        'nombrePlantilla' => $plantilla->nombrePlantilla,
                        'ciudad' => $ciudad,
                        'totalDestinatarios' => 0
                    ];
                }

                $resultado[$clave]['totalDestinatarios'] += $plantilla->numeroDestinatarios;
            } catch (\Exception $e) {
                Log::error("Error en plantilla: {$e->getMessage()}");
                throw $e;
            }
        }

        return collect(array_values($resultado));
    }

    public function headings(): array
    {
        return ['Nombre de Plantilla', 'Ciudad', 'Total de Destinatarios'];
    }

    public function title(): string
    {
        return 'Plantillas';
    }

    private function extraerCiudad($body)
    {
        $ciudades = ['acacias', 'villavicencio', 'guamal', 'castilla'];
        $normalizedBody = $this->normalizeText($body);

        foreach ($ciudades as $ciudad) {
            if (strpos($normalizedBody, $this->normalizeText($ciudad)) !== false) {
                return ucfirst($ciudad);
            }
        }
        return 'Sin ciudad';
    }

    private function normalizeText($text)
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $text);
        return $text;
    }
}

class StatusSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $messages;

    public function __construct($messages)
    {
        $this->messages = $messages;
        Log::info('Procesando hoja de estados.');
    }

    public function collection()
    {
        $results = [];

        foreach ($this->messages as $message) {
            try {
                $city = $this->extractCity($message->body);
                if (!isset($results[$city])) {
                    $results[$city] = ['entregados' => 0, 'fallidos' => 0, 'total' => 0];
                }

                if (in_array($message->status, ['sent', 'delivered', 'read'])) {
                    $results[$city]['entregados']++;
                } elseif ($message->status === 'failed') {
                    $results[$city]['fallidos']++;
                }

                $results[$city]['total']++;
            } catch (\Exception $e) {
                Log::error("Error al procesar estado del mensaje: {$e->getMessage()}");
                throw $e;
            }
        }

        return collect(array_map(function ($key, $val) {
            return array_merge(['ciudad' => $key], $val);
        }, array_keys($results), $results));
    }

    public function headings(): array
    {
        return ['Ciudad', 'Entregados', 'Fallidos', 'Total'];
    }

    public function title(): string
    {
        return 'Estados';
    }

    private function extractCity($body)
    {
        $cities = ['acacias', 'villavicencio', 'guamal', 'castilla'];
        $normalizedBody = $this->normalizeText($body);

        foreach ($cities as $city) {
            if (strpos($normalizedBody, $this->normalizeText($city)) !== false) {
                return ucfirst($city);
            }
        }
        return 'Sin ciudad';
    }

    private function normalizeText($text)
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $text);
        return $text;
    }
}
