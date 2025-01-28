<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Group;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Jobs\SendNewsletterJob;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = Newsletter::query();

        // Filtro por nombre (si aplica)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Paginación (10 boletines por página)
        $newsletters = $query->paginate(10);

        return view('newsletters.index', compact('newsletters'));
    }

    public function create()
    {
        $groups = Group::all();
        return view('newsletters.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'copy_email' => 'nullable|email|max:255',
            'attachment' => 'nullable|file|mimes:pdf|max:10240', // Solo PDF de hasta 10 MB
            'content' => 'required|string',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,id',
        ]);

        $newsletter = new Newsletter($validated);
        $newsletter->groups()->sync($validated['groups']);

        if ($request->hasFile('attachment')) {
            $originalName = $request->file('attachment')->getClientOriginalName();
            $timestamp = now()->format('Ymd_His'); // Formato: AñoMesDía_HoraMinutoSegundo
            $newFileName = $timestamp . '_' . $originalName; // Combinar fecha y nombre original
            $path = $request->file('attachment')->storeAs('attachments', $newFileName, 'public');
            $newsletter->has_attachment = true;
            $newsletter->attachment_path = $path;
        }

        $newsletter->save();

        return redirect()->route('newsletters.index')->with('success', 'Boletín creado exitosamente.');
    }

    public function edit(Newsletter $newsletter)
    {
        $groups = Group::all();
        return view('newsletters.edit', compact('newsletter', 'groups'));
    }

    public function update(Request $request, Newsletter $newsletter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'copy_email' => 'nullable|email|max:255',
            'attachment' => 'nullable|file|mimes:pdf|max:2048',
            'content' => 'required|string',
            'groups' => 'required|array',
            'groups.*' => 'exists:groups,id',
        ]);

        $newsletter->fill($validated);
        $newsletter->groups()->sync($validated['groups']);

        if ($request->hasFile('attachment')) {
            // Elimina el archivo anterior si existe
            if ($newsletter->attachment_path) {
                \Storage::disk('public')->delete($newsletter->attachment_path);
            }

            // Sube el nuevo archivo con fecha y hora
            $originalName = $request->file('attachment')->getClientOriginalName();
            $timestamp = now()->format('Ymd_His');
            $newFileName = $timestamp . '_' . $originalName;
            $path = $request->file('attachment')->storeAs('attachments', $newFileName, 'public');
            $newsletter->has_attachment = true;
            $newsletter->attachment_path = $path;
        }

        $newsletter->save();

        return redirect()->route('newsletters.index')->with('success', 'Boletín actualizado exitosamente.');
    }

    public function destroy(Newsletter $newsletter)
    {
        // Eliminar el archivo adjunto si existe
        if ($newsletter->attachment_path) {
            \Storage::disk('public')->delete($newsletter->attachment_path);
        }

        // Eliminar el boletín de la base de datos
        $newsletter->delete();

        return redirect()->route('newsletters.index')->with('success', 'Boletín eliminado exitosamente.');
    }

    public function sendTest(Request $request, Newsletter $newsletter)
    {
        $request->validate([
            'test_email' => 'required|email', // Validar el correo ingresado
        ]);

        // Enviar correo usando colas
        $testEmail = $request->test_email;
        Mail::to($testEmail)->send(new \App\Mail\NewsletterTestMail($newsletter));

        return redirect()->route('newsletters.index')->with('success', 'El boletín de prueba se envió correctamente.');
    }

    public function send(Request $request, Newsletter $newsletter)
    {
        $validated = $request->validate([
            'send_type' => 'required|in:immediate,scheduled',
            'scheduled_date' => 'nullable|date|after:now',
        ]);

        if ($validated['send_type'] === 'immediate') {
            // Enviar inmediatamente
            dispatch(new SendNewsletterJob($newsletter));
            return redirect()->route('newsletters.index')->with('success', 'El boletín se está enviando.');
        } else {
            // Programar envío
            $scheduledDate = Carbon::parse($validated['scheduled_date']);
            dispatch(new SendNewsletterJob($newsletter))->delay($scheduledDate);
            return redirect()->route('newsletters.index')->with('success', 'El boletín ha sido programado.');
        }
    }



}
