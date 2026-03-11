<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

class EmailField extends TextField
{
    public static function type(): string  { return 'email'; }
    public static function label(): string { return 'Email Input'; }
    public static function icon(): string  { return 'heroicon-o-envelope'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'input_type' => 'email',
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        // Always enforce email format, regardless of whether input_type is explicitly set.
        $fieldConfig['input_type'] = 'email';

        return parent::validationRules($fieldConfig);
    }
}
