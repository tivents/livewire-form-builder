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
    public int|string|null $formId = null;
    public string  $name     = '';
    public string  $description = '';
    public bool    $isActive = true;

    // ─── Schema: flat list of field configs ───────────────────────────
    public array $schema = [];

    // ─── Form-level settings ──────────────────────────────────────────
    public array $settings = ['button_color' => 'green', 'button_align' => 'left', 'button_label' => 'Submit'];

    // ─── Builder state ────────────────────────────────────────────────
    public ?int  $selectedFieldIndex = null;
    public ?int  $selectedChildIndex = null;   // set when a row child is selected
    public ?string $dragOverIndex    = null;
    public string  $activeTab        = 'builder';  // builder | preview | json

    // ─── Notifications ────────────────────────────────────────────────
    public ?string $flashMessage = null;
    public string  $flashType    = 'success';

    // ─── Lifecycle ────────────────────────────────────────────────────

    public function mount(int|string|null $formId = null): void
    {
        if ($formId) {
            $repo = app(FormRepositoryContract::class);
            $form = $repo->findOrFail($formId);
            $this->formId      = $formId;
            $this->name        = $form->name ?? '';
            $this->description = $form->description ?? '';

            if(isset($form->schema)) {
                $this->schema = is_array($form->schema) ? $form->schema : (json_decode($form->schema, true) ?? []);
            } else {
                $this->schema = [];
            }

            if (isset($form->settings)) {
                $loaded = is_array($form->settings) ? $form->settings : (json_decode($form->settings, true) ?? []);
                $this->settings = array_merge($this->settings, $loaded);
            }
        }
    }

    // ─── Palette: standard presets ───────────────────────────────────

    public function addPreset(string $presetKey, ?int $afterIndex = null): void
    {
        $presets = $this->getPresets();
        if (!isset($presets[$presetKey])) return;

        $config = $presets[$presetKey];

        // Avoid duplicate keys — append random suffix if key already used
        $existingKeys = array_column($this->schema, 'key');
        if (in_array($config['key'], $existingKeys)) {
            $config['key'] = $config['key'] . '_' . Str::random(4);
        }

        if ($afterIndex !== null && isset($this->schema[$afterIndex])) {
            array_splice($this->schema, $afterIndex + 1, 0, [$config]);
            $this->selectedFieldIndex = $afterIndex + 1;
        } else {
            $this->schema[] = $config;
            $this->selectedFieldIndex = count($this->schema) - 1;
        }

        $this->dispatch('field-added', index: $this->selectedFieldIndex);
    }

    protected function getPresets(): array
    {
        $base = \Tivents\LivewireFormBuilder\Support\FieldTypes\TextField::defaultConfig();

        return [
            'firstName' => array_merge($base, [
                'key'        => 'firstName',
                'type'       => 'text',
                'label'      => 'Vorname',
                'input_type' => 'text',
                'width'      => '1/2',
            ]),
            'lastName' => array_merge($base, [
                'key'        => 'lastName',
                'type'       => 'text',
                'label'      => 'Nachname',
                'input_type' => 'text',
                'width'      => '1/2',
            ]),
            'email' => array_merge($base, [
                'key'        => 'email',
                'type'       => 'text',
                'label'      => 'E-Mail',
                'input_type' => 'email',
                'width'      => 'full',
            ]),
        ];
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

    public function selectField(int $index, ?int $childIndex = null): void
    {
        if ($this->selectedFieldIndex === $index && $this->selectedChildIndex === $childIndex) {
            $this->selectedFieldIndex = null;
            $this->selectedChildIndex = null;
        } else {
            $this->selectedFieldIndex = $index;
            $this->selectedChildIndex = $childIndex;
        }
    }

    public function deleteField(int $index): void
    {
        array_splice($this->schema, $index, 1);
        $this->selectedFieldIndex = null;
        $this->selectedChildIndex = null;
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

    public function updateChildConfig(int $rowIndex, int $childIndex, string $property, mixed $value): void
    {
        data_set($this->schema, "{$rowIndex}.children.{$childIndex}.{$property}", $value);
        // Force Livewire to detect the nested array mutation
        $this->schema = $this->schema;
    }

    public function updateFieldOption(int $fieldIndex, int $optionIndex, string $prop, string $value): void
    {
        $this->schema[$fieldIndex]['options'][$optionIndex][$prop] = $value;
    }

    public function addFieldOption(int $fieldIndex, ?int $childIndex = null): void
    {
        if ($childIndex !== null) {
            $n = count($this->schema[$fieldIndex]['children'][$childIndex]['options'] ?? []) + 1;
            $this->schema[$fieldIndex]['children'][$childIndex]['options'][] = ['label' => "Option $n", 'value' => 'option_' . $n];
        } else {
            $n = count($this->schema[$fieldIndex]['options'] ?? []) + 1;
            $this->schema[$fieldIndex]['options'][] = ['label' => "Option $n", 'value' => 'option_' . $n];
        }
    }

    public function removeFieldOption(int $fieldIndex, int $optionIndex, ?int $childIndex = null): void
    {
        if ($childIndex !== null) {
            array_splice($this->schema[$fieldIndex]['children'][$childIndex]['options'], $optionIndex, 1);
        } else {
            array_splice($this->schema[$fieldIndex]['options'], $optionIndex, 1);
        }
    }

    // ─── Conditions ──────────────────────────────────────────────────

    public function addCondition(int $fieldIndex, ?int $childIndex = null): void
    {
        $rule = ['field' => '', 'operator' => '==', 'value' => ''];
        if ($childIndex !== null) {
            $this->schema[$fieldIndex]['children'][$childIndex]['conditions']['rules'][] = $rule;
            $this->schema[$fieldIndex]['children'][$childIndex]['conditions']['action'] ??= 'show';
            $this->schema[$fieldIndex]['children'][$childIndex]['conditions']['logic']  ??= 'and';
        } else {
            $this->schema[$fieldIndex]['conditions']['rules'][] = $rule;
            $this->schema[$fieldIndex]['conditions']['action'] ??= 'show';
            $this->schema[$fieldIndex]['conditions']['logic']  ??= 'and';
        }
    }

    public function removeCondition(int $fieldIndex, int $ruleIndex, ?int $childIndex = null): void
    {
        if ($childIndex !== null) {
            array_splice($this->schema[$fieldIndex]['children'][$childIndex]['conditions']['rules'], $ruleIndex, 1);
        } else {
            array_splice($this->schema[$fieldIndex]['conditions']['rules'], $ruleIndex, 1);
        }
    }

    // ─── Row children ────────────────────────────────────────────────

    public function addPresetToRow(int $rowIndex, string $presetKey): void
    {
        if (!isset($this->schema[$rowIndex]) || ($this->schema[$rowIndex]['type'] ?? '') !== 'row') return;

        $presets = $this->getPresets();
        if (!isset($presets[$presetKey])) return;

        $config = $presets[$presetKey];

        $existingKeys = array_column($this->schema[$rowIndex]['children'] ?? [], 'key');
        if (in_array($config['key'], $existingKeys)) {
            $config['key'] = $config['key'] . '_' . Str::random(4);
        }

        $this->schema[$rowIndex]['children'][] = $config;
        $newChildIndex = count($this->schema[$rowIndex]['children']) - 1;

        $this->selectedFieldIndex = $rowIndex;
        $this->selectedChildIndex = $newChildIndex;
        $this->dispatch('field-added', index: $rowIndex);
    }

    public function addFieldToRow(int $rowIndex, string $type): void
    {
        if (!isset($this->schema[$rowIndex]) || ($this->schema[$rowIndex]['type'] ?? '') !== 'row') return;

        $registry = app(FieldRegistry::class);
        $class    = $registry->get($type);

        $config        = $class::defaultConfig();
        $config['key'] = $this->generateKey($type);
        $config['type']= $type;

        $this->schema[$rowIndex]['children'][] = $config;
        $newChildIndex = count($this->schema[$rowIndex]['children']) - 1;

        $this->selectedFieldIndex = $rowIndex;
        $this->selectedChildIndex = $newChildIndex;
        $this->dispatch('field-added', index: $rowIndex);
    }

    public function removeFieldFromRow(int $rowIndex, int $childIndex): void
    {
        if (!isset($this->schema[$rowIndex]['children'][$childIndex])) return;

        array_splice($this->schema[$rowIndex]['children'], $childIndex, 1);

        if ($this->selectedFieldIndex === $rowIndex && $this->selectedChildIndex === $childIndex) {
            $this->selectedChildIndex = null;
        }
    }

    public function moveChildInRow(int $rowIndex, int $from, int $to): void
    {
        $children = $this->schema[$rowIndex]['children'] ?? [];
        if (!isset($children[$from]) || !isset($children[$to])) return;

        $child = array_splice($children, $from, 1)[0];
        array_splice($children, $to, 0, [$child]);

        $this->schema[$rowIndex]['children'] = $children;
        $this->selectedChildIndex = $to;
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
            'settings'    => $this->settings,
        ];

        $form = $this->formId
            ? $repo->update($this->formId, $data)
            : $repo->create($data);

        $this->flash('Form saved successfully!');
        $this->dispatch('form-saved', formId: $this->formId);
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
        if ($this->selectedChildIndex !== null) {
            return $this->schema[$this->selectedFieldIndex]['children'][$this->selectedChildIndex] ?? null;
        }
        return $this->schema[$this->selectedFieldIndex] ?? null;
    }

    public function getFieldKeysProperty(): array
    {
        $keys = [];
        $skip = ['heading', 'hint', 'html', 'row'];
        foreach ($this->schema as $field) {
            $type = $field['type'] ?? '';
            if ($type === 'row') {
                foreach ($field['children'] ?? [] as $child) {
                    $ct = $child['type'] ?? '';
                    if (!in_array($ct, $skip) && isset($child['key'])) {
                        $keys[$child['key']] = $child['label'] ?? $child['key'];
                    }
                }
            } elseif (!in_array($type, $skip) && isset($field['key'])) {
                $keys[$field['key']] = $field['label'] ?? $field['key'];
            }
        }
        return $keys;
    }

    public function render()
    {
        return view('livewire-form-builder::builder.index', [
            'palette'            => $this->palette,
            'presets'            => $this->getPresets(),
            'selectedField'      => $this->selectedField,
            'selectedChildIndex' => $this->selectedChildIndex,
            'fieldKeys'          => $this->fieldKeys,
        ]);
    }
}
