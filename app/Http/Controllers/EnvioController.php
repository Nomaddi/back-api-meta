<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function index()
    {
        $envios = Envio::all();
        return view('envios.index',[
            'envios' => $envios
        ]
    );
    }
}
