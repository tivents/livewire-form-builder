<?php

/**
 * EXAMPLE: ApiWebhookRepository
 *
 * A repository decorator that saves submissions to your database (via any
 * existing FormRepositoryContract implementation) AND forwards them to an
 * external HTTP endpoint (webhook, CRM, iPaaS, …) in one atomic step.
 *
 * ─── Setup ───────────────────────────────────────────────────────────────
 *
 * 1. Copy this file to app/Repositories/ApiWebhookRepository.php
 * 2. Adjust the constructor and the sendToApi() method to your needs.
 * 3. Register the binding in app/Providers/AppServiceProvider.php:
 *
 *     use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
 *     use App\Repositories\LivewireFormBuilderRepository;
 *     use App\Repositories\ApiWebhookRepository;
 *
 *     public function register(): void
 *     {
 *         $this->app->bind(FormRepositoryContract::class, function () {
 *             return new ApiWebhookRepository(
 *                 inner:      new LivewireFormBuilderRepository(),
 *                 webhookUrl: config('services.webhook.url'),  // or hard-coded string
 *                 secret:     config('services.webhook.secret'),
 *             );
 *         });
 *     }
 *
 * ─── How it works ────────────────────────────────────────────────────────
 *
 * Every method is delegated to the inner (Eloquent) repository unchanged.
 * Only saveSubmission() adds the webhook call after the DB write succeeds.
 * A failed HTTP call is logged but does NOT roll back the submission — adjust
 * the catch block if you need all-or-nothing behaviour.
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;

class ApiWebhookRepository implements FormRepositoryContract
{
    public function __construct(
        private readonly FormRepositoryContract $inner,
        private readonly string                 $webhookUrl,
        private readonly string                 $secret = '',
    ) {}

    // ── Submissions ───────────────────────────────────────────────────────

    public function saveSubmission(int|string $formId, array $data, array $meta = []): object
    {
        // 1. Persist via the inner (Eloquent) repo first.
        $submission = $this->inner->saveSubmission($formId, $data, $meta);

        // 2. Forward to external API – fire & forget, logged on failure.
        $this->sendToApi($formId, $submission, $data, $meta);

        return $submission;
    }

    // ── Delegate everything else to the inner repository ─────────────────

    public function findOrFail(int|string $id): object                                    { return $this->inner->findOrFail($id); }
    public function create(array $data): object                                           { return $this->inner->create($data); }
    public function update(int|string $id, array $data): object                           { return $this->inner->update($id, $data); }
    public function delete(int|string $id): void                                          { $this->inner->delete($id); }
    public function paginate(int $perPage = 25): mixed                                    { return $this->inner->paginate($perPage); }
    public function paginateSubmissions(int|string $formId, int $perPage = 25): mixed    { return $this->inner->paginateSubmissions($formId, $perPage); }
    public function findSubmissionOrFail(int|string $formId, int|string $id): object     { return $this->inner->findSubmissionOrFail($formId, $id); }
    public function deleteSubmission(int|string $submissionId): void                     { $this->inner->deleteSubmission($submissionId); }

    // ── Internal ──────────────────────────────────────────────────────────

    private function sendToApi(int|string $formId, object $submission, array $data, array $meta): void
    {
        try {
            $payload = [
                'event'         => 'form.submitted',
                'submission_id' => $submission->id,
                'form_id'       => $formId,
                'submitted_at'  => $submission->created_at?->toIso8601String(),
                'ip'            => $meta['ip'] ?? null,
                'data'          => $data,
            ];

            $request = Http::timeout(5)
                ->acceptJson()
                ->asJson();

            // Optional HMAC signature so the receiver can verify authenticity:
            //   X-Signature: sha256( secret + json_body )
            if ($this->secret !== '') {
                $body      = json_encode($payload);
                $signature = hash_hmac('sha256', $body, $this->secret);
                $request   = $request->withHeaders(['X-Signature' => $signature]);
            }

            $response = $request->post($this->webhookUrl, $payload);

            if ($response->failed()) {
                Log::warning('form-builder webhook failed', [
                    'url'    => $this->webhookUrl,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            // Log and swallow — the submission is already saved, never let a
            // broken webhook undo user data or show a 500 to the visitor.
            Log::error('form-builder webhook exception', [
                'url'     => $this->webhookUrl,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
