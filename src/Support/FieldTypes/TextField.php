<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class TextField extends AbstractFieldType
{
    public static function type(): string  { return 'text'; }
    public static function label(): string { return 'Text Input'; }
    public static function icon(): string  { return 'heroicon-o-pencil'; }
    public static function group(): string { return 'inputs'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'input_type'  => 'text',   // text | email | tel | url | number | password
            'min_length'  => null,
            'max_length'  => null,
            'pattern'     => null,
            'autocomplete'=> null,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules = parent::validationRules($fieldConfig);

        $inputType = $fieldConfig['input_type'] ?? 'text';

        if ($inputType === 'email')  $rules[] = 'email';
        if ($inputType === 'url')    $rules[] = 'url';
        if ($inputType === 'number') $rules[] = 'numeric';

        if (!empty($fieldConfig['min_length'])) $rules[] = 'min:' . $fieldConfig['min_length'];
        if (!empty($fieldConfig['max_length'])) $rules[] = 'max:' . $fieldConfig['max_length'];
        if (!empty($fieldConfig['pattern']))    $rules[] = 'regex:/' . $fieldConfig['pattern'] . '/';

        return $rules;
    }
}
