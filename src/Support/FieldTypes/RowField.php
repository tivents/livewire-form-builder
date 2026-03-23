<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

/**
 * Layout container that groups multiple fields side-by-side in a shared row.
 * Children are rendered inside a nested 12-column grid.
 */
class RowField extends AbstractFieldType
{
    public static function type(): string  { return 'row'; }
    public static function label(): string { return 'Row'; }
    public static function icon(): string  { return 'heroicon-o-view-columns'; }
    public static function group(): string { return 'layout'; }

    public static function defaultConfig(): array
    {
        return [
            'type'     => 'row',
            'key'      => '',
            'width'    => 'full',
            'children' => [],
        ];
    }

    public function validationRules(array $fieldConfig): array
    {
        return [];
    }

    public function render(array $fieldConfig, mixed $value = null): string
    {
        return '';
    }

    public function renderSettings(array $fieldConfig): string
    {
        return '';
    }
}
