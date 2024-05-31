<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldValue extends Model
{
    use HasFactory;

    protected $fillable = ['contacto_id', 'custom_field_id', 'value'];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class, 'contacto_id');
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }
}
