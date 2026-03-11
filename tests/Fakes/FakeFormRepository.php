<?php

namespace Tivents\LivewireFormBuilder\Tests\Fakes;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;

/**
 * In-memory implementation of FormRepositoryContract for tests.
 * Stores state in static arrays so tests can inspect it directly.
 */
class FakeFormRepository implements FormRepositoryContract
{
    public static array $forms = [];
    public static array $submissions = [];
    private static int $nextFormId = 1;
    private static int $nextSubmissionId = 1;

    public static function reset(): void
    {
        static::$forms = [];
        static::$submissions = [];
        static::$nextFormId = 1;
        static::$nextSubmissionId = 1;
    }

    public function findOrFail(int|string $id): object
    {
        if (!isset(static::$forms[$id])) {
            throw (new ModelNotFoundException)->setModel('Form', $id);
        }

        return static::$forms[$id];
    }

    public function create(array $data): object
    {
        $id   = static::$nextFormId++;
        $form = (object) array_merge([
            'id'          => $id,
            'name'        => '',
            'description' => null,
            'is_active'   => true,
            'schema'      => [],
            'settings'    => null,
        ], $data);

        static::$forms[$id] = $form;

        return $form;
    }

    public function update(int|string $id, array $data): object
    {
        $form = $this->findOrFail($id);

        foreach ($data as $key => $value) {
            $form->$key = $value;
        }

        return $form;
    }

    public function delete(int|string $id): void
    {
        $this->findOrFail($id);
        unset(static::$forms[$id]);
    }

    public function paginate(int $perPage = 25): mixed
    {
        $items = array_values(static::$forms);

        return new LengthAwarePaginator($items, count($items), $perPage);
    }

    public function saveSubmission(int|string $formId, array $data, array $meta = []): object
    {
        $id         = static::$nextSubmissionId++;
        $submission = (object) array_merge([
            'id'         => $id,
            'form_id'    => $formId,
            'data'       => $data,
            'ip'         => null,
            'user_agent' => null,
            'read_at'    => null,
        ], $meta);

        static::$submissions[$id] = $submission;

        return $submission;
    }

    public function paginateSubmissions(int|string $formId, int $perPage = 25): mixed
    {
        $items = array_values(
            array_filter(static::$submissions, fn ($s) => $s->form_id == $formId)
        );

        return new LengthAwarePaginator($items, count($items), $perPage);
    }

    public function findSubmissionOrFail(int|string $formId, int|string $submissionId): object
    {
        $submission = static::$submissions[$submissionId] ?? null;

        if (!$submission || $submission->form_id != $formId) {
            throw (new ModelNotFoundException)->setModel('FormSubmission', $submissionId);
        }

        return $submission;
    }

    public function deleteSubmission(int|string $submissionId): void
    {
        if (!isset(static::$submissions[$submissionId])) {
            throw (new ModelNotFoundException)->setModel('FormSubmission', $submissionId);
        }

        unset(static::$submissions[$submissionId]);
    }
}
