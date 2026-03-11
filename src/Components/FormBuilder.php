<?php

namespace Tivents\LivewireFormBuilder\Components;

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use Tivents\LivewireFormBuilder\Support\FieldRegistry;

/**
 * Drag-and-drop form builder Livewire component.
 *
 * Usage:
 *   <livewire:livewire-form-builder::builder />                  — create new form
 *   <livewire:livewire-form-builder::builder :form-id="$id" />  — edit existing
 */
class FormBuilder extends Component
{
    // ─── Form meta ────────────────────────────────────────────────────
    public ?int    $formId   = null;
    public string  $name     = '';
    public string  $description = '';
    public bool    $isActive = true;

    // ─── Schema: flat list of field configs ───────────────────────────
    public array $schema = [];

    // ─── Builder state ────────────────────────────────────────────────
    public ?int  $selectedFieldIndex = null;
    public ?string $dragOverIndex    = null;
    public string  $activeTab        = 'builder';  // builder | preview | json

    // ─── Notifications ────────────────────────────────────────────────
    public ?string $flashMessage = null;
    public string  $flashType    = 'success';

    // ─── Lifecycle ────────────────────────────────────────────────────

    public function mount(?int $formId = null): void
    {
        if ($formId) {
            $repo = app(FormRepositoryContract::class);
            $form = $repo->findOrFail($formId);
            $this->formId      = $form->id;
            $this->name        = $form->name;
            $this->description = $form->description ?? '';
            $this->isActive    = (bool) $form->is_active;
            $this->schema      = is_array($form->schema) ? $form->schema : (json_decode($form->schema, true) ?? []);
        }
    }

    // ─── Palette: add a field ─────────────────────────────────────────

    public function addField(string $type, ?int $afterIndex = null): void
    {
        $registry = app(FieldRegistry::class);
        $class    = $registry->get($type);

        $config        = $class::defaultConfig();
        $config['key'] = $this->generateKey($type);
        $config['type']= $type;

        if ($afterIndex !== null && isset($this->schema[$afterIndex])) {
            array_splice($this->schema, $afterIndex + 1, 0, [$config]);
            $this->selectedFieldIndex = $afterIndex + 1;
        } else {
            $this->schema[] = $config;
            $this->selectedFieldIndex = count($this->schema) - 1;
        }

        $this->dispatch('field-added', index: $this->selectedFieldIndex);
    }

    // ─── Drag & Drop ─────────────────────────────────────────────────

    #[On('reorder-fields')]
    public function reorderFields(array $orderedKeys): void
    {
        $indexed = collect($this->schema)->keyBy('key');
        $reordered = [];

        foreach ($orderedKeys as $key) {
            if ($indexed->has($key)) {
                $reordered[] = $indexed[$key];
            }
        }

        $this->schema = $reordered;

        if ($this->selectedFieldIndex !== null) {
            // Re-locate selected field by key
            $selectedKey = $this->schema[$this->selectedFieldIndex]['key'] ?? null;
            if ($selectedKey) {
                foreach ($this->schema as $i => $f) {
                    if ($f['key'] === $selectedKey) {
                        $this->selectedFieldIndex = $i;
                        break;
                    }
                }
            }
        }
    }

    public function moveField(int $from, int $to): void
    {
        if (!isset($this->schema[$from]) || !isset($this->schema[$to])) return;

        $field = array_splice($this->schema, $from, 1)[0];
        array_splice($this->schema, $to, 0, [$field]);

        $this->selectedFieldIndex = $to;
    }

    public function moveUp(int $index): void
    {
        if ($index > 0) $this->moveField($index, $index - 1);
    }

    public function moveDown(int $index): void
    {
        if ($index < count($this->schema) - 1) $this->moveField($index, $index + 1);
    }

    // ─── Field selection & deletion ──────────────────────────────────

    public function selectField(int $index): void
    {
        $this->selectedFieldIndex = ($this->selectedFieldIndex === $index) ? null : $index;
    }

    public function deleteField(int $index): void
    {
        array_splice($this->schema, $index, 1);
        $this->selectedFieldIndex = null;
    }

    public function duplicateField(int $index): void
    {
        $copy        = $this->schema[$index];
        $copy['key'] = $this->generateKey($copy['type'] ?? 'field');
        array_splice($this->schema, $index + 1, 0, [$copy]);
        $this->selectedFieldIndex = $index + 1;
    }

    // ─── Field settings update ───────────────────────────────────────

    public function updateFieldConfig(int $index, string $property, mixed $value): void
    {
        data_set($this->schema, "{$index}.{$property}", $value);
    }

    public function updateFieldOption(int $fieldIndex, int $optionIndex, string $prop, string $value): void
    {
        $this->schema[$fieldIndex]['options'][$optionIndex][$prop] = $value;
    }

    public function addFieldOption(int $fieldIndex): void
    {
        $n = count($this->schema[$fieldIndex]['options'] ?? []) + 1;
        $this->schema[$fieldIndex]['options'][] = [
            'label' => "Option $n",
            'value' => 'option_' . $n,
        ];
    }

    public function removeFieldOption(int $fieldIndex, int $optionIndex): void
    {
        array_splice($this->schema[$fieldIndex]['options'], $optionIndex, 1);
    }

    // ─── Conditions ──────────────────────────────────────────────────

    public function addCondition(int $fieldIndex): void
    {
        $this->schema[$fieldIndex]['conditions']['rules'][] = [
            'field'    => '',
            'operator' => '==',
            'value'    => '',
        ];
        $this->schema[$fieldIndex]['conditions']['action'] ??= 'show';
        $this->schema[$fieldIndex]['conditions']['logic']  ??= 'and';
    }

    public function removeCondition(int $fieldIndex, int $ruleIndex): void
    {
        array_splice($this->schema[$fieldIndex]['conditions']['rules'], $ruleIndex, 1);
    }

    // ─── Repeater children ───────────────────────────────────────────

    public function addChildField(int $repeaterIndex, string $type): void
    {
        $registry = app(FieldRegistry::class);
        $class    = $registry->get($type);
        $config   = $class::defaultConfig();
        $config['key']  = $this->generateKey($type . '_child');
        $config['type'] = $type;
        $this->schema[$repeaterIndex]['children'][] = $config;
    }

    public function deleteChildField(int $repeaterIndex, int $childIndex): void
    {
        array_splice($this->schema[$repeaterIndex]['children'], $childIndex, 1);
    }

    // ─── Persist ─────────────────────────────────────────────────────

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $repo = app(FormRepositoryContract::class);

        $data = [
            'name'        => $this->name,
            'description' => $this->description,
            'is_active'   => $this->isActive,
            'schema'      => $this->schema,
        ];

        $form = $this->formId
            ? $repo->update($this->formId, $data)
            : $repo->create($data);

        $this->formId = $form->id;
        $this->flash('Form saved successfully!');
        $this->dispatch('form-saved', formId: $form->id);
    }

    // ─── JSON import / export ────────────────────────────────────────

    public function exportJson(): string
    {
        return json_encode([
            'name'        => $this->name,
            'description' => $this->description,
            'schema'      => $this->schema,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function importJson(string $json): void
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->flash('Invalid JSON.', 'error');
            return;
        }

        $this->name        = $data['name']        ?? $this->name;
        $this->description = $data['description'] ?? $this->description;
        $this->schema      = $data['schema']       ?? [];
        $this->selectedFieldIndex = null;
        $this->flash('Schema imported.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    protected function generateKey(string $type): string
    {
        return Str::snake($type) . '_' . Str::random(6);
    }

    protected function flash(string $message, string $type = 'success'): void
    {
        $this->flashMessage = $message;
        $this->flashType    = $type;
    }

    public function dismissFlash(): void
    {
        $this->flashMessage = null;
    }

    // ─── Computed helpers exposed to view ────────────────────────────

    public function getPaletteProperty(): array
    {
        return app(FieldRegistry::class)->palette();
    }

    public function getSelectedFieldProperty(): ?array
    {
        if ($this->selectedFieldIndex === null) return null;
        return $this->schema[$this->selectedFieldIndex] ?? null;
    }

    public function getFieldKeysProperty(): array
    {
        return collect($this->schema)
            ->filter(fn ($f) => !in_array($f['type'] ?? '', ['heading', 'hint', 'html']))
            ->pluck('label', 'key')
            ->toArray();
    }

    public function render()
    {
        return view('livewire-form-builder::builder.index', [
            'palette'       => $this->palette,
            'selectedField' => $this->selectedField,
            'fieldKeys'     => $this->fieldKeys,
        ]);
    }
}
