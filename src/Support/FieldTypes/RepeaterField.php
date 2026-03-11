<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class RepeaterField extends AbstractFieldType
{
    public static function type(): string  { return 'repeater'; }
    public static function label(): string { return 'Repeater Group'; }
    public static function icon(): string  { return 'heroicon-o-rectangle-stack'; }
    public static function group(): string { return 'advanced'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'min_rows'   => 1,
            'max_rows'   => null,
            'add_label'  => 'Add item',
            'children'   => [],  // nested field schema
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules = parent::validationRules($fieldConfig);
        $rules[] = 'array';
        if (!empty($fieldConfig['min_rows'])) $rules[] = 'min:' . $fieldConfig['min_rows'];
        if (!empty($fieldConfig['max_rows'])) $rules[] = 'max:' . $fieldConfig['max_rows'];
        return $rules;
    }
}
