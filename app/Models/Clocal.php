<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clocal extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa',
        'id_pdf',
        'contrato',
        'codigo_contrato',
        'tipo_orden_id',
        'orden_servicio',
        'desc_general_act',
        'objeto',
        'requerimientos',
        'tiempo_ejecucion',
        'fecha_inicio',
        'fecha_recibo',
        'hora_limite',
        'tag',
        'publicacion',
        'estado',
        'status'
    ];

}
