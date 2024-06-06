<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ErrorLogController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos entrantes
        $request->validate([
            'message' => 'required|string',
            'status' => 'required|string',
            'response' => 'nullable|string',
            'url' => 'nullable|string',
        ]);

        // Formatear el mensaje de error
        $errorMessage = [
            'message' => $request->input('message'),
            'status' => $request->input('status'),
            'response' => $request->input('response'),
            'url' => $request->input('url'),
            'timestamp' => now()->toDateTimeString()
        ];

        // Guardar el error en el log
        Log::error('Client Error: ', $errorMessage);

        return response()->json(['status' => 'Error logged successfully'], 200);
    }
}
