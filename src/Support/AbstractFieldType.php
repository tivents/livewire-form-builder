<?php

namespace Tivents\LivewireFormBuilder\Support;

use Tivents\LivewireFormBuilder\Contracts\FieldTypeContract;

abstract class AbstractFieldType implements FieldTypeContract
{
    public static function group(): string
    {
        return 'inputs';
    }

    public static function defaultConfig(): array
    {
        return [
            'key'          => '',
            'label'        => static::label(),
            'placeholder'  => '',
            'hint'         => '',
            'required'     => false,
            'disabled'     => false,
            'hidden'       => false,
            'width'        => 'full',   // full | 1/2 | 1/3 | 2/3 | 1/4 | 3/4
            'conditions'   => [],        // conditional logic rules
            'validations'  => [],        // extra validation rules
        ];
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules = [];

        if (!empty($fieldConfig['required'])) {
            $rules[] = 'required';
        } else {
            $rules[] = 'nullable';
        }

        return $rules;
    }

    public function render(array $fieldConfig, mixed $value = null): string
    {
        return view('livewire-form-builder::fields.' . static::type(), [
            'field' => $fieldConfig,
            'value' => $value,
        ])->render();
    }

    public function renderSettings(array $fieldConfig): string
    {
        return view('livewire-form-builder::settings.' . static::type(), [
            'field' => $fieldConfig,
        ])->render();
    }

    /**
     * Resolve column-width Tailwind classes from the "width" setting.
     */
    public static function widthClass(string $width): string
    {
        return match ($width) {
            '1/2'  => 'col-span-6',
            '1/3'  => 'col-span-4',
            '2/3'  => 'col-span-8',
            '1/4'  => 'col-span-3',
            '3/4'  => 'col-span-9',
            default => 'col-span-12',
        };
    }

    /**
     * Inline grid-column style — framework-agnostic alternative to widthClass().
     * Use this when the host app's CSS (Bootstrap etc.) may override col-span-* classes.
     */
    public static function widthStyle(string $width): string
    {
        $span = match ($width) {
            '1/2'  => 6,
            '1/3'  => 4,
            '2/3'  => 8,
            '1/4'  => 3,
            '3/4'  => 9,
            default => 12,
        };
        return "grid-column: span {$span} / span {$span};";
    }
}
