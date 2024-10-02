<?php

namespace App\Services;

use OpenAI\Client;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
    }

    // Función para hablar con la AI
    public function getResponse(string $prompt): string
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-4-turbo',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }

    // Función para subir un archivo CSV y procesarlo
    public function analyzeCsv(string $filePath): string
    {
        $content = file_get_contents($filePath);
        $prompt = "Analyze the following CSV content:\n" . $content;
        
        return $this->getResponse($prompt);
    }

    // Función para subir un archivo PDF y procesarlo
    public function analyzePdf(string $filePath): string
    {
        // Utiliza una librería para extraer el texto del PDF, como `smalot/pdfparser`
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();
        
        $prompt = "Analyze the following PDF content:\n" . $text;
        
        return $this->getResponse($prompt);
    }
}
