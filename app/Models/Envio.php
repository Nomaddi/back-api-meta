<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombrePlantilla',
        'numeroDestinatarios',
        'body',
        'tag',
        'sent_messages',
        'status',
    ];

    protected $casts = [
        'tag' => 'array',
    ];

}
