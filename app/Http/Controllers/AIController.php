<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Ai;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    public function index()
    {
        $ais = Ai::all();
        return view('ai.index',[
            'ais'=> $ais
        ]);
    }

    public function store(Request $request)
    {
        

        $ai = Ai::create([
            'nombre' => $request->nombre,
            'descripcion' => $request-> descripcion,
            'api_key' => $request -> api_key,
            'organizacion' => $request ->organizacion,
            'asistente_id'=> $request -> asistente_id,
        ]);

        $user = Auth::user();

        // Asociar el usuario con la aplicación recién creada
        if ($user) {
            $user->ais()->attach($ai->id);
        }

        return response()->json(['success' => 'Número creado con éxito.']);


        // Numeros::create($request->all());

        // return response()->json(['success' => 'Numero creado con éxito.']);

    }

    public function destroy(Ai $ai)
    {
        $ai->delete();
        return response()->json(['success' => 'Registro eliminado con éxito.']);
    }


    public function update(Request $request, $id)
    {
        $ai = Ai::findOrFail($id);
        $ai->update($request->all());
        return response()->json(['success' => 'AI actualizada con éxito.']);
    }


    // public function uploadPDF(Request $request)
    // {
    //     $request->validate([
    //         'pdf' => 'required|mimes:pdf|max:2048'
    //     ]);

    //     $fileName = time().'.'.$request->pdf->extension();  
    //     $request->pdf->storeAs('pdfs', $fileName, 'public');

    //     return back()->with('success', 'Archivo subido con éxito');
    // }

    // public function pauseIA() 
    // {
    //     // Aquí cambiarías el estado de la IA en la base de datos
    //     $status = 'paused';
    //     // Por ejemplo, almacenar el estado en una tabla
    //     DB::table('ia_status')->update(['status' => $status]);
    //     return back()->with('success', 'La IA ha sido pausada.');
    // }

    // public function resumeIA()
    // {
    //     $status = 'active';
    //     DB::table('ia_status')->update(['status' => $status]);
    //     return back()->with('success', 'La IA ha sido reanudada.');
    // }

}
