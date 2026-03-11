<?php

namespace Tivents\LivewireFormBuilder\Contracts;

interface FieldTypeContract
{
    /**
     * The unique machine name (e.g. "text", "select").
     */
    public static function type(): string;

    /**
     * Human-readable label shown in the builder palette.
     */
    public static function label(): string;

    /**
     * Heroicon or SVG string for the palette icon.
     */
    public static function icon(): string;

    /**
     * Group in the palette sidebar (e.g. "inputs", "layout", "advanced").
     */
    public static function group(): string;

    /**
     * Default configuration for a newly dragged field.
     */
    public static function defaultConfig(): array;

    /**
     * Laravel validation rules for this field based on its config.
     * Returns an array suitable for Validator::make().
     */
    public function validationRules(array $fieldConfig): array;

    /**
     * Render the field for the form renderer (frontend view).
     */
    public function render(array $fieldConfig, mixed $value = null): string;

    /**
     * Render the settings panel shown when the field is selected in the builder.
     */
    public function renderSettings(array $fieldConfig): string;
}
