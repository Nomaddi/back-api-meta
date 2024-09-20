<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AIController extends Controller
{
    public function showUploadForm()
    {
        return view('ai.upload');
    }

    public function uploadPDF(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:2048'
        ]);

        $fileName = time().'.'.$request->pdf->extension();  
        $request->pdf->storeAs('pdfs', $fileName, 'public');

        return back()->with('success', 'Archivo subido con éxito');
    }

    public function pauseIA() 
    {
        // Aquí cambiarías el estado de la IA en la base de datos
        $status = 'paused';
        // Por ejemplo, almacenar el estado en una tabla
        DB::table('ia_status')->update(['status' => $status]);
        return back()->with('success', 'La IA ha sido pausada.');
    }

    public function resumeIA()
    {
        $status = 'active';
        DB::table('ia_status')->update(['status' => $status]);
        return back()->with('success', 'La IA ha sido reanudada.');
    }

    

}

