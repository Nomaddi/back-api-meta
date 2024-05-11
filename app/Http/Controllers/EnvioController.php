<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Envio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnvioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $envios = $user->envios()->get();
        $tags = $user->tags()->get()->keyBy('id');

        return view(
            'envios.index',
            [
                'envios' => $envios,
                'tags' => $tags,
            ]
        );
    }


}
