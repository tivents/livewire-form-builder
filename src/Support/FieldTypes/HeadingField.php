<?php
namespace Tivents\LivewireFormBuilder\Support\FieldTypes;
use Tivents\LivewireFormBuilder\Support\AbstractFieldType;
class HeadingField extends AbstractFieldType {
    public static function type(): string  { return 'heading'; }
    public static function label(): string { return 'Heading'; }
    public static function icon(): string  { return 'heroicon-o-h1'; }
    public static function group(): string { return 'layout'; }
    public static function defaultConfig(): array {
        return array_merge(parent::defaultConfig(), ['text' => 'Section Heading', 'level' => 'h2', 'required' => false]);
    }
    public function validationRules(array $fieldConfig): array { return []; }
}
