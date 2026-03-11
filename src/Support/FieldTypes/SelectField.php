<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class SelectField extends AbstractFieldType
{
    public static function type(): string  { return 'select'; }
    public static function label(): string { return 'Select / Multi-Select'; }
    public static function icon(): string  { return 'heroicon-o-chevron-up-down'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'multiple'  => false,
            'searchable'=> false,
            'options'   => [],
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules   = parent::validationRules($fieldConfig);
        $options = $fieldConfig['options'] ?? [];

        if (!empty($options)) {
            $values = collect($options)->pluck('value')->implode(',');

            if (!empty($fieldConfig['multiple'])) {
                $rules[] = 'array';
            }

            $rules[] = 'in:' . $values;
        }

        return $rules;
    }
}
