<?php

namespace App\Exports;

use Illuminate\Bus\Queueable;
use App\Exports\MessagesDataSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MessagesSheet implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new MessagesDataSheet($this->data);
        return $sheets;
    }

}




