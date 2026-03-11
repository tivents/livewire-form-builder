<?php
namespace Tivents\LivewireFormBuilder\Support\FieldTypes;
use Tivents\LivewireFormBuilder\Support\AbstractFieldType;
class HtmlField extends AbstractFieldType {
    public static function type(): string  { return 'html'; }
    public static function label(): string { return 'Custom HTML'; }
    public static function icon(): string  { return 'heroicon-o-code-bracket'; }
    public static function group(): string { return 'layout'; }
    public static function defaultConfig(): array {
        return array_merge(parent::defaultConfig(), ['content' => '<p>Custom content here…</p>', 'required' => false]);
    }
    public function validationRules(array $fieldConfig): array { return []; }
}
