<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class RadioField extends AbstractFieldType
{
    public static function type(): string  { return 'radio'; }
    public static function label(): string { return 'Radio Buttons'; }
    public static function icon(): string  { return 'heroicon-o-radio'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'options' => [
                ['label' => 'Option 1', 'value' => 'option_1'],
                ['label' => 'Option 2', 'value' => 'option_2'],
            ],
            'inline' => false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules  = parent::validationRules($fieldConfig);
        $values = collect($fieldConfig['options'] ?? [])->pluck('value')->implode(',');
        $rules[] = 'in:' . $values;
        return $rules;
    }
}
