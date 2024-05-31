<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\CustomField;
use Illuminate\Http\Request;
use App\Models\CustomFieldValue;

class CustomFieldController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:custom_fields.index')->only('index');
        $this->middleware('can:custom_fields.store')->only('store');
        $this->middleware('can:custom_fields.update')->only('update');
        $this->middleware('can:custom_fields.destroy')->only('destroy');
    }
    public function index()
    {
        $customFields = auth()->user()->customFields ?? collect();
        return view('custom_fields.index', compact('customFields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        CustomField::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return response()->json(['success' => 'Campo personalizado creado exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
        ]);

        $customField = CustomField::findOrFail($id);
        $customField->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return response()->json(['success' => 'Campo personalizado actualizado exitosamente.']);
    }

    public function destroy($id)
    {
        $customField = CustomField::findOrFail($id);
        $customField->delete();

        return response()->json(['success' => 'Campo personalizado eliminado exitosamente.']);
    }
}
