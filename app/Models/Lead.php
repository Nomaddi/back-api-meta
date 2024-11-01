<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'bot_id',
        'detalles',
        'estado',
    ];

    protected $casts = [
        'detalles' => 'array',
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }
}
