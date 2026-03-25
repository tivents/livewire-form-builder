<?php

namespace Tivents\LivewireFormBuilder\Components;

use Livewire\Component;
use Livewire\WithPagination;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;

/**
 * Embeddable submissions viewer.
 *
 * Usage (minimal):
 *   <livewire:livewire-form-builder::submissions :form-id="$form->id" />
 *
 * Full example with custom field names:
 *   <livewire:livewire-form-builder::submissions
 *       :form-id="$productId"
 *       id-field="code"
 *       date-field="submitted_at"
 *       :extra-columns="[
 *           ['field' => 'ordernumber', 'label' => 'Order No.'],
 *           ['field' => 'ip',         'label' => 'IP'],
 *       ]"
 *       :per-page="10"
 *       :allow-delete="false"
 *   />
 *
 * Props:
 *   form-id        (required) Form identifier passed to the repository.
 *   id-field       Name of the primary-key column on the submission object. Default: "id".
 *   date-field     Name of the timestamp column to display. Default: "created_at".
 *   extra-columns  Array of ['field' => '...', 'label' => '...'] for extra submission columns
 *                  (direct properties on the submission, NOT inside the data payload).
 *   per-page       Rows per page. Default: 25.
 *   allow-delete   Show delete buttons. Default: true.
 */
class SubmissionsViewer extends Component
{
    use WithPagination;

    // ─── Props ────────────────────────────────────────────────────────
    public int|string $formId;
    public string     $idField      = 'id';
    public string     $dateField    = 'created_at';
    public array      $extraColumns = [];   // [['field' => 'ordernumber', 'label' => 'Order No.']]
    public int        $perPage      = 25;
    public bool       $allowDelete  = true;

    // ─── Runtime ──────────────────────────────────────────────────────
    public ?string $activeSubmissionId = null;
    public array   $activeSubmission   = [];
    public array   $activeSubmissionMeta = [];  // extra-column values for the open submission
    public array   $schema             = [];
    public string  $formName           = '';

    // ─── Lifecycle ────────────────────────────────────────────────────

    public function mount(
        int|string $formId,
        string     $idField      = 'id',
        string     $dateField    = 'created_at',
        array      $extraColumns = [],
        int        $perPage      = 25,
        bool       $allowDelete  = true,
    ): void {
        $this->formId       = $formId;
        $this->idField      = $idField;
        $this->dateField    = $dateField;
        $this->extraColumns = $extraColumns;
        $this->perPage      = $perPage;
        $this->allowDelete  = $allowDelete;

        $repo = app(FormRepositoryContract::class);
        $form = $repo->findOrFail($formId);

        $this->formName = $form->name ?? '';
        $rawSchema      = $form->schema ?? [];
        $this->schema   = is_array($rawSchema) ? $rawSchema : (json_decode($rawSchema, true) ?? []);
    }

    // ─── Actions ──────────────────────────────────────────────────────

    public function viewSubmission(int|string $submissionId): void
    {
        $repo       = app(FormRepositoryContract::class);
        $submission = $repo->findSubmissionOrFail($this->formId, $submissionId);

        $this->activeSubmissionId = (string) $submissionId;

        $data = $submission->data ?? [];
        $this->activeSubmission = is_array($data) ? $data : (json_decode($data, true) ?? []);

        // Collect extra-column values from the submission object
        $this->activeSubmissionMeta = [];
        foreach ($this->extraColumns as $col) {
            $f = $col['field'] ?? null;
            if ($f) {
                $this->activeSubmissionMeta[$f] = $submission->{$f} ?? null;
            }
        }
    }

    public function closeDetail(): void
    {
        $this->activeSubmissionId   = null;
        $this->activeSubmission     = [];
        $this->activeSubmissionMeta = [];
    }

    public function deleteSubmission(int|string $submissionId): void
    {
        if (! $this->allowDelete) return;

        $repo = app(FormRepositoryContract::class);
        $repo->deleteSubmission($submissionId);

        if ($this->activeSubmissionId === (string) $submissionId) {
            $this->closeDetail();
        }
    }

    // ─── Computed ─────────────────────────────────────────────────────

    /** Flat ordered list of (key => label) pairs derived from the schema. */
    public function getFieldLabelsProperty(): array
    {
        $labels = [];
        foreach ($this->schema as $field) {
            if (($field['type'] ?? '') === 'row') {
                foreach ($field['children'] ?? [] as $child) {
                    $k = $child['key'] ?? null;
                    if ($k) $labels[$k] = $child['label'] ?? $k;
                }
                continue;
            }
            $k = $field['key'] ?? null;
            if ($k) $labels[$k] = $field['label'] ?? $k;
        }
        return $labels;
    }

    // ─── Render ───────────────────────────────────────────────────────

    public function render()
    {
        $repo        = app(FormRepositoryContract::class);
        $submissions = $repo->paginateSubmissions($this->formId, $this->perPage);

        return view('livewire-form-builder::submissions.viewer', [
            'submissions' => $submissions,
            'fieldLabels' => $this->fieldLabels,
        ]);
    }
}
