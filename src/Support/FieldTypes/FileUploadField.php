<?php

namespace Tivents\LivewireFormBuilder\Support\FieldTypes;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class FileUploadField extends AbstractFieldType
{
    public static function type(): string  { return 'file'; }
    public static function label(): string { return 'File Upload'; }
    public static function icon(): string  { return 'heroicon-o-paper-clip'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            'multiple'          => false,
            'max_size_kb'       => 10240,
            'allowed_types'     => [],  // e.g. ['image/*', '.pdf']
            'max_files'         => 5,
        ]);
    }

    public function validationRules(array $fieldConfig): array
    {
        $rules    = parent::validationRules($fieldConfig);
        $maxKb    = $fieldConfig['max_size_kb'] ?? config('livewire-form-builder.max_file_size', 10240);
        $mimes    = $this->resolveMimes($fieldConfig['allowed_types'] ?? []);
        $fileRule = 'file|max:' . $maxKb;
        if ($mimes) $fileRule .= '|mimes:' . $mimes;

        if (!empty($fieldConfig['multiple'])) {
            $rules[] = 'array';
            $rules[] = 'max:' . ($fieldConfig['max_files'] ?? 5);
            // Laravel validates each item with wildcard key separately – handled in FormRenderer
        } else {
            $rules[] = $fileRule;
        }

        return $rules;
    }

    protected function resolveMimes(array $types): string
    {
        $ext = [];
        foreach ($types as $t) {
            if (str_contains($t, '/')) {
                // convert mime to extension hint
                $ext[] = explode('/', $t)[1];
            } elseif (str_starts_with($t, '.')) {
                $ext[] = ltrim($t, '.');
            }
        }
        return implode(',', $ext);
    }
}
