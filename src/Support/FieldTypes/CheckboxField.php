<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class CheckboxField extends AbstractFieldType
{
    public static function type(): string  { return 'checkbox'; }
    public static function label(): string { return 'Checkbox'; }
    public static function icon(): string  { return 'heroicon-o-check-circle'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'options' => [
                ['label' => 'Option 1', 'value' => 'option_1'],
            ],
            'inline'  => false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        return parent::validationRules($fieldConfig);
    }
}
