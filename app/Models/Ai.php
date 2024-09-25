<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ai extends Model
{
    use HasFactory;

    protected $fillable = [ // Definimos qué campos pueden ser asignados de forma masiva al crear o actualizar una instancia de este modelo.
        'nombre',
        'descripcion',
        'api_key',
        'organizacion',
        'asistente_id',
    ];

    // Método para definir la relación muchos a muchos (belongsToMany) con el modelo User.
    // Esto indica que una IA puede estar asociada a varios usuarios y un usuario puede estar asociado a varias IA.
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_ai', 'ai_id', 'user_id');
    }
}

//app\Models\Ai.php 
