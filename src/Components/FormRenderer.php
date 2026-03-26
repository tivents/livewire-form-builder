<?php

namespace Tivents\LivewireFormBuilder\Components;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use Tivents\LivewireFormBuilder\Support\ConditionalLogic;
use Tivents\LivewireFormBuilder\Support\FieldRegistry;

/**
 * Public-facing form renderer.
 *
 * Usage:
 *   <livewire:livewire-form-builder::renderer :form-id="$form->id" />
 *   <livewire:livewire-form-builder::renderer :schema="$schemaArray" />
 */
class FormRenderer extends Component
{
    use WithFileUploads;

    // ─── Props ────────────────────────────────────────────────────────
    public int|string|null $formId       = null;
    public int|string|null $submissionId = null;
    public bool    $showHidden = false;
    public array   $schema   = [];
    public string  $formName = '';

    // ─── Form-level settings ──────────────────────────────────────────
    public array $settings = ['button_color' => 'green', 'button_align' => 'left', 'button_label' => 'Submit'];

    // ─── Runtime data ─────────────────────────────────────────────────
    public array $formData        = [];
    public array $validationErrors= [];
    public bool  $submitted       = false;
    public string $successMessage = 'Thank you! Your response has been recorded.';

    // ─── Extra fields (injected from outside, not part of schema) ────
    public array $extraFields = [];
    public array $extraData   = [];

    // ─── File uploads (handled separately by Livewire) ───────────────
    public array $fileUploads = [];

    // ─── Lifecycle ────────────────────────────────────────────────────

    public function mount(
        int|string|null $formId       = null,
        int|string|null $submissionId = null,
        bool  $showHidden    = false,
        array $schema        = [],
        array $initialData   = [],
        string $successMessage = '',
        string $redirectUrl    = '',
        array $extraFields     = [],
    ): void {
        // Schema passed directly → use it as-is, no repository call needed.
        // formId may still be provided alongside schema to associate submissions.
        if ($schema) {
            // Accept either a flat fields array or a full form object ({ name, schema, settings, ... })
            $this->schema   = isset($schema['schema']) ? $schema['schema'] : $schema;
            $this->formName = $schema['name'] ?? '';
            if (isset($schema['settings'])) {
                $this->settings = array_merge($this->settings, $schema['settings']);
            }
            $this->formId = $formId;
        } elseif ($formId) {
            // No schema passed → fetch everything from the repository.
            $repo = app(FormRepositoryContract::class);
            $form = $repo->findOrFail($formId);
            $this->formId   = $formId;
            $this->schema   = is_array($form->schema) ? $form->schema : (json_decode($form->schema, true) ?? []);
            $this->formName = $form->name ?? '';
            if (isset($form->settings)) {
                $loaded = is_array($form->settings) ? $form->settings : (json_decode($form->settings, true) ?? []);
                $this->settings = array_merge($this->settings, $loaded);
            }
        }

        // Edit mode: store submission ID for update on submit
        $this->submissionId = $submissionId;
        $this->showHidden   = $showHidden;

        // If editing and no initialData provided, fetch submission from repository
        if ($submissionId && !$initialData && $this->formId) {
            $repo        = app(FormRepositoryContract::class);
            $submission  = $repo->findSubmissionOrFail($this->formId, $submissionId);
            $initialData = is_array($submission->data) ? $submission->data : (json_decode($submission->data, true) ?? []);
        }

        if ($successMessage) {
            $this->successMessage = $successMessage;
        }

        // Prop overrides any redirect_url from schema settings
        if ($redirectUrl) {
            $this->settings['redirect_url'] = $redirectUrl;
        }

        // Initialise extra fields
        $this->extraFields = $extraFields;
        foreach ($extraFields as $field) {
            $key = $field['key'] ?? null;
            if ($key) $this->extraData[$key] = $field['default'] ?? null;
        }

        // Initialise formData keys — prefer initialData values over field defaults
        foreach ($this->schema as $field) {
            if (($field['type'] ?? '') === 'row') {
                foreach ($field['children'] ?? [] as $child) {
                    $ck = $child['key'] ?? null;
                    if ($ck) $this->formData[$ck] = $initialData[$ck] ?? $child['default'] ?? null;
                }
                continue;
            }
            $key = $field['key'] ?? null;
            if (!$key) continue;
            $this->formData[$key] = $initialData[$key] ?? $field['default'] ?? null;
        }
    }

    // ─── Real-time updates ───────────────────────────────────────────

    public function updated(string $property): void
    {
        // Strip 'formData.' prefix for per-field validation
        if (str_starts_with($property, 'formData.')) {
            $fieldKey = substr($property, strlen('formData.'));
            $this->validateField($fieldKey);
        }
    }

    // ─── Submission ──────────────────────────────────────────────────

    public function submit(): void
    {
        $this->validationErrors = [];

        // Only validate visible fields
        $visibilityMap = ConditionalLogic::visibilityMap($this->schema, $this->formData);
        $rules         = $this->buildValidationRules($visibilityMap);

        $validator = Validator::make($this->formData, $rules);
        if ($validator->fails()) {
            $this->validationErrors = $validator->errors()->toArray();
            return;
        }

        // Merge extra fields into submission data
        $data = array_merge($this->formData, $this->extraData);
        foreach ($this->fileUploads as $key => $file) {
            if ($file) {
                $path = $file->store(
                    config('livewire-form-builder.upload_directory', 'livewire-form-builder/uploads'),
                    config('livewire-form-builder.disk', 'public')
                );
                $data[$key] = $path;
            }
        }

        // Persist via repository
        $meta = ['ip' => request()->ip(), 'user_agent' => request()->userAgent()];
        if ($this->submissionId) {
            // Edit mode: update existing submission
            $repo = app(FormRepositoryContract::class);
            $repo->updateSubmission($this->submissionId, $data, $meta);
            $this->dispatch('form-updated', submissionId: $this->submissionId, formId: $this->formId, data: $data);
        } elseif ($this->formId) {
            // Create mode: save new submission
            $repo = app(FormRepositoryContract::class);
            $repo->saveSubmission($this->formId, $data, $meta);
            $this->dispatch('form-submitted', formId: $this->formId, data: $data);
        } else {
            $this->dispatch('form-submitted', formId: $this->formId, data: $data);
        }

        $redirectUrl = $this->settings['redirect_url'] ?? null;
        if ($redirectUrl) {
            $this->redirect($redirectUrl, navigate: false);
            return;
        }

        $this->submitted = true;
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    protected function buildValidationRules(array $visibilityMap): array
    {
        $registry = app(FieldRegistry::class);
        $rules    = [];

        foreach ($this->schema as $field) {
            $key  = $field['key'] ?? null;
            $type = $field['type'] ?? null;

            if (!$type) continue;

            // Row: validate children directly as flat top-level fields
            if ($type === 'row') {
                foreach ($field['children'] ?? [] as $child) {
                    $ck = $child['key'] ?? null;
                    $ct = $child['type'] ?? null;
                    if (!$ck || !$ct || !$registry->has($ct)) continue;
                    if (!($visibilityMap[$ck] ?? true)) continue;
                    $childType  = $registry->make($ct);
                    $childRules = $childType->validationRules($child);
                    if ($childRules) $rules[$ck] = $childRules;
                }
                continue;
            }

            if (!$key) continue;
            if (!($visibilityMap[$key] ?? true)) continue;  // skip hidden fields

            if (!$registry->has($type)) continue;

            $fieldType = $registry->make($type);
            $fieldRules= $fieldType->validationRules($field);

            if ($fieldRules) {
                $rules[$key] = $fieldRules;
            }

            // Repeater: add rules for child fields
            if ($type === 'repeater' && !empty($field['children'])) {
                $rules[$key] = ['nullable', 'array'];
                foreach ($field['children'] as $child) {
                    $ck = $child['key'] ?? null;
                    $ct = $child['type'] ?? null;
                    if (!$ck || !$ct || !$registry->has($ct)) continue;
                    $childType  = $registry->make($ct);
                    $childRules = $childType->validationRules($child);
                    if ($childRules) {
                        $rules[$key . '.*.' . $ck] = $childRules;
                    }
                }
            }
        }

        return $rules;
    }

    protected function validateField(string $fieldKey): void
    {
        $field = collect($this->schema)->firstWhere('key', $fieldKey);

        // If not found at top level, search inside row children
        if (!$field) {
            foreach ($this->schema as $f) {
                if (($f['type'] ?? '') === 'row') {
                    $found = collect($f['children'] ?? [])->firstWhere('key', $fieldKey);
                    if ($found) { $field = $found; break; }
                }
            }
        }

        if (!$field) return;

        $registry  = app(FieldRegistry::class);
        $type      = $field['type'] ?? null;
        if (!$type || !$registry->has($type)) return;

        $fieldType = $registry->make($type);
        $rules     = $fieldType->validationRules($field);

        $validator = Validator::make(
            [$fieldKey => $this->formData[$fieldKey] ?? null],
            [$fieldKey => $rules]
        );

        if ($validator->fails()) {
            $this->validationErrors[$fieldKey] = $validator->errors()->get($fieldKey);
        } else {
            unset($this->validationErrors[$fieldKey]);
        }
    }

    public function getVisibilityMapProperty(): array
    {
        return ConditionalLogic::visibilityMap($this->schema, $this->formData);
    }

    public function render()
    {
        return view('livewire-form-builder::renderer.index', [
            'visibilityMap' => $this->visibilityMap,
            'settings'      => $this->settings,
            'showHidden'    => $this->showHidden,
        ]);
    }
}
