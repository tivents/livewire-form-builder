<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class DateTimeField extends AbstractFieldType
{
    public static function type(): string  { return 'datetime'; }
    public static function label(): string { return 'Date / Time'; }
    public static function icon(): string  { return 'heroicon-o-calendar-days'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'mode'      => 'date',   // date | time | datetime
            'min_date'  => null,
            'max_date'  => null,
            'format'    => null,     // display format hint
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules = parent::validationRules($fieldConfig);
        $mode  = $fieldConfig['mode'] ?? 'date';

        if ($mode === 'date')     $rules[] = 'date';
        if ($mode === 'datetime') $rules[] = 'date';
        if ($mode === 'time')     $rules[] = 'date_format:H:i';

        if (!empty($fieldConfig['min_date'])) $rules[] = 'after_or_equal:' . $fieldConfig['min_date'];
        if (!empty($fieldConfig['max_date'])) $rules[] = 'before_or_equal:' . $fieldConfig['max_date'];

        return $rules;
    }
}
