<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class ToggleField extends AbstractFieldType
{
    public static function type(): string  { return 'toggle'; }
    public static function label(): string { return 'Toggle (Yes/No)'; }
    public static function icon(): string  { return 'heroicon-o-adjustments-horizontal'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'on_label'  => 'Yes',
            'off_label' => 'No',
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules   = parent::validationRules($fieldConfig);
        $rules[] = 'boolean';
        return $rules;
    }
}
