<?php

/**
 * EXAMPLE: FormSubmissionListener  (Laravel Event Listener)
 *
 * Use this approach when you want to react to a submission AFTER it has been
 * saved to the database — e.g. send a CRM lead, trigger a notification, or
 * call an automation pipeline — without touching the repository.
 *
 * ─── How it works ────────────────────────────────────────────────────────
 *
 * The custom repository (or the default one) fires a Laravel event:
 *
 *   event(new FormSubmitted($submission));
 *
 * This listener handles that event and calls the external API.
 * Because it runs synchronously by default you can trivially queue it by
 * adding `implements ShouldQueue`.
 *
 * ─── Setup ───────────────────────────────────────────────────────────────
 *
 * 1. Create the event class (see FormSubmitted at the bottom of this file).
 * 2. Copy this listener to app/Listeners/FormSubmissionListener.php
 * 3. Register in app/Providers/EventServiceProvider.php  (Laravel ≤ 10)
 *    or via the #[ListensTo] attribute (Laravel 11+):
 *
 *     // EventServiceProvider.php (L10 and below)
 *     protected $listen = [
 *         \App\Events\FormSubmitted::class => [
 *             \App\Listeners\FormSubmissionListener::class,
 *         ],
 *     ];
 *
 * 4. In your repository's saveSubmission(), fire the event after saving:
 *
 *     public function saveSubmission(int|string $formId, array $data, array $meta = []): object
 *     {
 *         $submission = FormSubmission::create([...]);
 *         event(new \App\Events\FormSubmitted($submission));
 *         return $submission;
 *     }
 */

namespace App\Listeners;

use App\Events\FormSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Add "implements ShouldQueue" to process this in a background queue job.
class FormSubmissionListener /* implements ShouldQueue */
{
    public function handle(FormSubmitted $event): void
    {
        $submission = $event->submission;

        try {
            Http::timeout(10)
                ->acceptJson()
                ->post(config('services.crm.endpoint'), [
                    'source'      => 'form-builder',
                    'form_id'     => $submission->form_id,
                    'data'        => $submission->data,
                    'submitted_at'=> $submission->created_at?->toIso8601String(),
                ]);
        } catch (\Throwable $e) {
            Log::error('CRM push failed', ['message' => $e->getMessage()]);
        }
    }
}

// ─── Companion event class ────────────────────────────────────────────────
// Copy to app/Events/FormSubmitted.php

/*
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class FormSubmitted
{
    use Dispatchable;

    public function __construct(public readonly object $submission) {}
}
*/
