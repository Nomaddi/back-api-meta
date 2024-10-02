<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;

class OpenAIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    // Página principal del módulo OpenAI
    public function index()
    {
        return view('openai.index');
    }

    // Función para manejar el chat con OpenAI
    public function chat(Request $request)
    {
        if ($request->isMethod('post')) {
            $prompt = $request->input('prompt');
            $response = $this->openAIService->getResponse($prompt);

            return view('openai.chat', compact('response'));
        }

        return view('openai.chat');
    }

    public function getResponse(Request $request)
    {
        $prompt = $request->input('prompt');
        $response = $this->openAIService->getResponse($prompt);

        return response()->json([
            'response' => $response,
        ]);
    }

    public function uploadCsv(Request $request)
    {
        if ($request->hasFile('csv')) {
            $file = $request->file('csv');
            $filePath = $file->getPathname();
            $response = $this->openAIService->analyzeCsv($filePath);

            return response()->json([
                'response' => $response,
            ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }

    public function uploadPdf(Request $request)
    {
        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $filePath = $file->getPathname();
            $response = $this->openAIService->analyzePdf($filePath);

            return response()->json([
                'response' => $response,
            ]);
        }

        return response()->json(['error' => 'No file uploaded.'], 400);
    }
}
