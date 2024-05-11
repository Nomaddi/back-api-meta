<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'contacto_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }
}
