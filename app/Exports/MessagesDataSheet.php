<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class MessagesDataSheet implements FromCollection, WithHeadings
{
    use Exportable;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return ['Nombre', 'Teléfono', 'Etiquetas', 'Mensaje', 'Estado', 'Fecha de Creación'];
    }
}
