<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class TextareaField extends AbstractFieldType
{
    public static function type(): string  { return 'textarea'; }
    public static function label(): string { return 'Textarea'; }
    public static function icon(): string  { return 'heroicon-o-document-text'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'rows'       => 4,
            'min_length' => null,
            'max_length' => null,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules = parent::validationRules($fieldConfig);
        if (!empty($fieldConfig['min_length'])) $rules[] = 'min:' . $fieldConfig['min_length'];
        if (!empty($fieldConfig['max_length'])) $rules[] = 'max:' . $fieldConfig['max_length'];
        return $rules;
    }
}
