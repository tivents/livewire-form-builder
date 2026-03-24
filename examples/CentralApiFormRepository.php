<?php

/**
 * EXAMPLE: CentralApiFormRepository
 *
 * A fully API-backed repository — no local database required.
 * All form and submission data is stored in and retrieved from a central
 * HTTP API. Use this when multiple independent Laravel apps (partner
 * backends, admin portals, …) share the same form schema store.
 *
 * ─── Architecture ────────────────────────────────────────────────────────
 *
 *  ┌────────────────────┐        ┌──────────────────────┐
 *  │  Partner-Backend   │──API──▶│  Central API / DB    │
 *  │  (this repo)       │        │                      │
 *  └────────────────────┘        └──────────────────────┘
 *  ┌────────────────────┐                  ▲
 *  │  Admin-Backend     │──API─────────────┘
 *  │  (same repo)       │
 *  └────────────────────┘
 *
 * ─── Expected API contract ───────────────────────────────────────────────
 *
 *  GET    /api/forms                       → { data: [...], meta: { current_page, last_page, per_page, total } }
 *  POST   /api/forms                       → { id, name, description, is_active, schema, ... }
 *  GET    /api/forms/{id}                  → { id, name, description, is_active, schema, ... }
 *  PUT    /api/forms/{id}                  → { id, name, description, is_active, schema, ... }
 *  DELETE /api/forms/{id}                  → 204 No Content
 *
 *  POST   /api/forms/{id}/submissions      → { id, form_id, data, meta, created_at, ... }
 *  GET    /api/forms/{id}/submissions      → { data: [...], meta: { current_page, last_page, per_page, total } }
 *  GET    /api/forms/{id}/submissions/{s}  → { id, form_id, data, meta, created_at, ... }
 *  DELETE /api/submissions/{id}            → 204 No Content
 *
 * ─── Setup ───────────────────────────────────────────────────────────────
 *
 * 1. Copy this file to app/Repositories/CentralApiFormRepository.php
 *
 * 2. Add to your .env:
 *
 *     CENTRAL_API_URL=https://central.example.com
 *     CENTRAL_API_TOKEN=your-bearer-token
 *
 * 3. Add to config/services.php:
 *
 *     'central' => [
 *         'url'   => env('CENTRAL_API_URL'),
 *         'token' => env('CENTRAL_API_TOKEN'),
 *     ],
 *
 * 4. Register in app/Providers/AppServiceProvider.php:
 *
 *     use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
 *     use App\Repositories\CentralApiFormRepository;
 *
 *     public function register(): void
 *     {
 *         $this->app->bind(FormRepositoryContract::class, function () {
 *             return new CentralApiFormRepository(
 *                 baseUrl: config('services.central.url'),
 *                 token:   config('services.central.token'),
 *             );
 *         });
 *     }
 *
 * ─── Notes ───────────────────────────────────────────────────────────────
 *
 * - All HTTP errors are rethrown — the package's exception handling will
 *   display them appropriately (404 → ModelNotFoundException equivalent).
 * - API responses are cast to objects so the package can access properties
 *   (e.g. $form->schema, $form->name) just like an Eloquent model.
 * - paginate() and paginateSubmissions() return a LengthAwarePaginator that
 *   mirrors the remote API's pagination meta, so Blade @paginate links work.
 */

namespace App\Repositories;

use Illuminate\Http\Client\RequestException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;

class CentralApiFormRepository implements FormRepositoryContract
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
    ) {}

    // ── Forms ─────────────────────────────────────────────────────────────

    public function findOrFail(int|string $id): object
    {
        $response = $this->client()
            ->get("{$this->baseUrl}/api/forms/{$id}");

        $this->throwIfFailed($response, "Form #{$id} not found.");

        return $this->toObject($response->json());
    }

    public function create(array $data): object
    {
        $response = $this->client()
            ->post("{$this->baseUrl}/api/forms", $data);

        $this->throwIfFailed($response, 'Could not create form.');

        return $this->toObject($response->json());
    }

    public function update(int|string $id, array $data): object
    {
        $response = $this->client()
            ->put("{$this->baseUrl}/api/forms/{$id}", $data);

        $this->throwIfFailed($response, "Could not update form #{$id}.");

        return $this->toObject($response->json());
    }

    public function delete(int|string $id): void
    {
        $response = $this->client()
            ->delete("{$this->baseUrl}/api/forms/{$id}");

        $this->throwIfFailed($response, "Could not delete form #{$id}.");
    }

    public function paginate(int $perPage = 25): mixed
    {
        $response = $this->client()
            ->get("{$this->baseUrl}/api/forms", ['per_page' => $perPage]);

        $this->throwIfFailed($response, 'Could not fetch forms.');

        return $this->toPaginator($response->json(), $perPage, route: '/livewire-form-builder');
    }

    // ── Submissions ───────────────────────────────────────────────────────

    public function saveSubmission(int|string $formId, array $data, array $meta = []): object
    {
        $response = $this->client()
            ->post("{$this->baseUrl}/api/forms/{$formId}/submissions", [
                'data' => $data,
                'meta' => $meta,
            ]);

        $this->throwIfFailed($response, "Could not save submission for form #{$formId}.");

        return $this->toObject($response->json());
    }

    public function paginateSubmissions(int|string $formId, int $perPage = 25): mixed
    {
        $response = $this->client()
            ->get("{$this->baseUrl}/api/forms/{$formId}/submissions", ['per_page' => $perPage]);

        $this->throwIfFailed($response, "Could not fetch submissions for form #{$formId}.");

        return $this->toPaginator($response->json(), $perPage);
    }

    public function findSubmissionOrFail(int|string $formId, int|string $submissionId): object
    {
        $response = $this->client()
            ->get("{$this->baseUrl}/api/forms/{$formId}/submissions/{$submissionId}");

        $this->throwIfFailed($response, "Submission #{$submissionId} not found.");

        return $this->toObject($response->json());
    }

    public function deleteSubmission(int|string $submissionId): void
    {
        $response = $this->client()
            ->delete("{$this->baseUrl}/api/submissions/{$submissionId}");

        $this->throwIfFailed($response, "Could not delete submission #{$submissionId}.");
    }

    // ── Internal helpers ──────────────────────────────────────────────────

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::withToken($this->token)
            ->acceptJson()
            ->asJson()
            ->timeout(10);
    }

    /**
     * Throw a ModelNotFoundException-compatible exception on 404,
     * or a generic RequestException on any other non-2xx response.
     */
    private function throwIfFailed(\Illuminate\Http\Client\Response $response, string $message): void
    {
        if ($response->status() === 404) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException($message);
        }

        if ($response->failed()) {
            throw new \RuntimeException(
                "{$message} API returned HTTP {$response->status()}: {$response->body()}"
            );
        }
    }

    /**
     * Cast an associative array (JSON decoded) to a plain object so that
     * the package can access properties like $form->schema, $form->name, etc.
     * Nested arrays remain arrays (schema, options, …) — only the top level
     * is cast, matching how Eloquent models expose their attributes.
     */
    private function toObject(array $data): object
    {
        return (object) $data;
    }

    /**
     * Build a LengthAwarePaginator from a paginated API response.
     *
     * Expected response shape:
     *   {
     *     "data": [ ... ],
     *     "meta": { "current_page": 1, "last_page": 3, "per_page": 25, "total": 72 }
     *   }
     *
     * Falls back gracefully when the central API uses a flat array response.
     */
    private function toPaginator(array $response, int $perPage, string $route = ''): LengthAwarePaginator
    {
        $items       = array_map(fn ($item) => $this->toObject($item), $response['data'] ?? $response);
        $meta        = $response['meta'] ?? [];
        $currentPage = $meta['current_page'] ?? 1;
        $total       = $meta['total']        ?? count($items);

        return new LengthAwarePaginator(
            items:       $items,
            total:       $total,
            perPage:     $perPage,
            currentPage: $currentPage,
            options:     $route !== '' ? ['path' => $route] : [],
        );
    }
}
