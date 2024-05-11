<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEnvios extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'envio_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function envio()
    {
        return $this->belongsTo(Envio::class);
    }
}
