<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class NumberField extends AbstractFieldType
{
    public static function type(): string  { return 'number'; }
    public static function label(): string { return 'Number'; }
    public static function icon(): string  { return 'heroicon-o-hashtag'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'min'  => null,
            'max'  => null,
            'step' => null,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules   = parent::validationRules($fieldConfig);
        $rules[] = 'numeric';
        if (!is_null($fieldConfig['min'] ?? null)) $rules[] = 'min:' . $fieldConfig['min'];
        if (!is_null($fieldConfig['max'] ?? null)) $rules[] = 'max:' . $fieldConfig['max'];
        return $rules;
    }
}
