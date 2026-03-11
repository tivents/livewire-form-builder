<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class HiddenField extends AbstractFieldType
{
    public static function type(): string  { return 'hidden'; }
    public static function label(): string { return 'Hidden Value'; }
    public static function icon(): string  { return 'heroicon-o-eye-slash'; }
    public static function group(): string { return 'advanced'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'default' => '',
            'required'=> false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        return ['nullable'];
    }
}
