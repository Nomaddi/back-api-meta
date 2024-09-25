<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Threads extends Model
{
    use HasFactory;

    protected $fillable = [
        'wa_id',
        'threads_id',
    ] ;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_thread', 'thread_id', 'user_id');
    }
}

//app\Models\Threads.php
