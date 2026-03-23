<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\FormSubmission; // Depends on your published model stub

/**
 * BEISPIEL: Verarbeitung von Formularübermittlungen via Controller.
 * 
 * Es gibt zwei Hauptwege, wie ein Controller "verarbeitet":
 * 
 * 1. VIA API (AJAX/FETCH)
 *    Der Form-Renderer sendet ein JS-Event 'form-submitted'. Ein Skript auf Ihrer
 *    Seite fängt dieses ab und sendet die Daten an einen API-Endpunkt Ihres Controllers.
 * 
 * 2. VIA EVENT LISTENER (Server-seitig)
 *    Ihr Repository feuert nach dem Speichern ein Laravel-Event. Ein Listener
 *    ruft daraufhin Logik in einem Service oder Controller auf.
 * 
 * Dieses Beispiel zeigt Weg 1: Empfang von Daten über eine Route.
 */
class FormSubmissionController extends Controller
{
    /**
     * Verarbeitet die Daten, die via JavaScript (fetch) gesendet wurden.
     * Siehe auch: examples/api-form-page.blade.php
     * 
     * Route in routes/api.php:
     * Route::post('/forms/process', [FormSubmissionController::class, 'handleApiSubmission']);
     */
    public function handleApiSubmission(Request $request)
    {
        // 1. Daten validieren (die vom JS-Event kommen)
        $validated = $request->validate([
            'form_id' => 'required',
            'fields'  => 'required|array',
            'source'  => 'nullable|string',
        ]);

        $data   = $validated['fields'];
        $formId = $validated['form_id'];

        // 2. Eigene Logik ausführen
        // Beispiel: Bestimmte E-Mails basierend auf Feldern senden
        if (isset($data['email'])) {
            // Mail::to($data['email'])->send(new \App\Mail\ThankYouMail($data));
        }

        // 3. In ein externes CRM pushen
        // Http::post('https://api.crm.com/leads', $data);

        // 4. Protokollieren
        Log::info("Formular #{$formId} wurde verarbeitet.", ['data' => $data]);

        return response()->json([
            'success' => true,
            'message' => 'Daten wurden erfolgreich vom Controller verarbeitet.',
        ]);
    }

    /**
     * Alternativer Weg: Wenn Sie die Submission-ID haben (nachdem das Repository gespeichert hat).
     * 
     * Route in routes/web.php:
     * Route::get('/submissions/{submission}/process', [FormSubmissionController::class, 'processById']);
     */
    public function processById(FormSubmission $submission)
    {
        // Hier haben Sie Zugriff auf das bereits in der DB gespeicherte Objekt
        $data = $submission->data;
        
        // Verarbeitungslogik...
        
        return back()->with('status', 'Übermittlung erneut verarbeitet!');
    }
}
