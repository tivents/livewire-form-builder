<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class HeadingField extends AbstractFieldType
{
    public static function type(): string  { return 'heading'; }
    public static function label(): string { return 'Heading'; }
    public static function icon(): string  { return 'heroicon-o-h1'; }
    public static function group(): string { return 'layout'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'text'     => 'Section Heading',
            'level'    => 'h2',   // h1 | h2 | h3 | h4
            'required' => false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        return [];  // layout fields have no validation
    }
}


class HintField extends AbstractFieldType
{
    public static function type(): string  { return 'hint'; }
    public static function label(): string { return 'Hint / Info Box'; }
    public static function icon(): string  { return 'heroicon-o-information-circle'; }
    public static function group(): string { return 'layout'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'text'     => 'This is a hint.',
            'style'    => 'info',    // info | warning | success | error
            'required' => false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        return [];
    }
}


class HtmlField extends AbstractFieldType
{
    public static function type(): string  { return 'html'; }
    public static function label(): string { return 'Custom HTML'; }
    public static function icon(): string  { return 'heroicon-o-code-bracket'; }
    public static function group(): string { return 'layout'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'content'  => '<p>Custom content here…</p>',
            'required' => false,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        return [];
    }
}
