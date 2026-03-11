<?php
namespace Tivents\LivewireFormBuilder\Support\FieldTypes;
use Tivents\LivewireFormBuilder\Support\AbstractFieldType;
class HintField extends AbstractFieldType {
    public static function type(): string  { return 'hint'; }
    public static function label(): string { return 'Hint / Info Box'; }
    public static function icon(): string  { return 'heroicon-o-information-circle'; }
    public static function group(): string { return 'layout'; }
    public static function defaultConfig(): array {
        return array_merge(parent::defaultConfig(), ['text' => 'This is a hint.', 'style' => 'info', 'required' => false]);
    }
    public function validationRules(array $fieldConfig): array { return []; }
}
