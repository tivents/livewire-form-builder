<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class DividerField extends AbstractFieldType
{
    public static function type(): string  { return 'divider'; }
    public static function label(): string { return 'Divider'; }
    public static function icon(): string  { return 'heroicon-o-minus'; }
    public static function group(): string { return 'layout'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'required' => false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        return [];
    }
}
