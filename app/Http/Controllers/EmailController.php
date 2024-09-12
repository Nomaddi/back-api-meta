<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'messageBody' => 'required|string',
        ]);

        $email = $validatedData['email'];
        $subject = $validatedData['subject'];
        $messageBody = $validatedData['messageBody'];

        try {
            // Despachar el trabajo a la cola
            SendEmailJob::dispatch($email, $subject, $messageBody)
                ->onQueue(env('DATABASE_EMAIL_QUEUE', 'database-email-queue'));

            return redirect()->back()->with('success', 'Correo enviado a la cola correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }
}
