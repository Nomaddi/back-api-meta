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
        'status',
    ];

    protected $casts = [
        'tag' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_envios');
    }
}
