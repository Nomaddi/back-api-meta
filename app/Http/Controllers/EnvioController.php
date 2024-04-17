<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EnvioController extends Controller
{
    public function index()
    {
        $envios = Envio::all();
        $tags = Tag::all()->keyBy('id');

        return view(
            'envios.index',
            [
                'envios' => $envios,
                'tags' => $tags,
            ]
        );
    }


}
