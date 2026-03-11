<?php

namespace Tivents\LivewireFormBuilder\Contracts;

/**
 * FormRepositoryContract
 *
 * The package ships no Model or Migration. The host application must:
 *
 *  1. Create its own tables (run `php artisan livewire-form-builder:publish-stubs`
 *     to get ready-made migration + model stubs).
 *  2. Create a repository class that implements this interface.
 *  3. Register the binding in a ServiceProvider or in config:
 *
 *     // config/livewire-form-builder.php
 *     'repository' => \App\Repositories\LivewireFormBuilderRepository::class,
 *
 *     // or in AppServiceProvider::register():
 *     $this->app->bind(
 *         \Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract::class,
 *         \App\Repositories\LivewireFormBuilderRepository::class,
 *     );
 */
interface FormRepositoryContract
{
    /**
     * Find a form by its primary key.
     * Must return an object / array with at least:
     *   id, name, description, is_active, schema (array)
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int|string $id): object;

    /**
     * Persist a new form and return it.
     */
    public function create(array $data): object;

    /**
     * Update an existing form and return it.
     */
    public function update(int|string $id, array $data): object;

    /**
     * Delete a form.
     */
    public function delete(int|string $id): void;

    /**
     * Return a paginated / collection list of all forms.
     * Shape of each item: { id, name, description, is_active, schema, ... }
     */
    public function paginate(int $perPage = 25): mixed;

    /**
     * Store a new form submission.
     * $data contains the validated field values (key => value).
     */
    public function saveSubmission(int|string $formId, array $data, array $meta = []): object;

    /**
     * Return paginated submissions for a form.
     */
    public function paginateSubmissions(int|string $formId, int $perPage = 25): mixed;

    /**
     * Find a single submission.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findSubmissionOrFail(int|string $formId, int|string $submissionId): object;

    /**
     * Delete a submission.
     */
    public function deleteSubmission(int|string $submissionId): void;
}
