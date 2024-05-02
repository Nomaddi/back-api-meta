<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function store(Request $request)
    {
        \Log::error('Client Error: ' . $request->input('error') . ' - Details: ' . $request->input('details'));

        return response()->json(['status' => 'Error logged']);
    }
}
