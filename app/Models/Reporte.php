<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;
    protected $fillable = ['fechaInicio', 'fechaFin', 'archivo'];

    public function archivoExiste()
    {
        return file_exists(storage_path('app/' . $this->archivo));
    }
}
