<?php

namespace Tivents\LivewireFormBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeFieldTypeCommand extends Command
{
    protected $signature = 'livewire-form-builder:make-field {name : PascalCase name, e.g. StarRating}';
    protected $description = 'Scaffold a new custom field type class + view stubs';

    public function handle(): int
    {
        $name      = Str::studly($this->argument('name'));
        $type      = Str::snake($name);          // e.g. star_rating
        $classFile = app_path("FormFields/{$name}Field.php");
        $viewDir   = resource_path("views/vendor/livewire-form-builder");

        // ── PHP class ─────────────────────────────────────────────────
        $this->ensureDir(app_path('FormFields'));

        if (file_exists($classFile)) {
            $this->warn("Class already exists: {$classFile}");
        } else {
            file_put_contents($classFile, $this->classStub($name, $type));
            $this->info("Created: {$classFile}");
        }

        // ── Blade views ───────────────────────────────────────────────
        $this->ensureDir("{$viewDir}/fields");
        $this->ensureDir("{$viewDir}/settings");

        $fieldView    = "{$viewDir}/fields/{$type}.blade.php";
        $settingsView = "{$viewDir}/settings/{$type}.blade.php";

        if (!file_exists($fieldView)) {
            file_put_contents($fieldView, $this->fieldViewStub($type));
            $this->info("Created: {$fieldView}");
        }

        if (!file_exists($settingsView)) {
            file_put_contents($settingsView, $this->settingsViewStub($name));
            $this->info("Created: {$settingsView}");
        }

        $this->newLine();
        $this->line("Next steps:");
        $this->line("  1. Edit <fg=cyan>App\\FormFields\\{$name}Field</> to implement your field logic.");
        $this->line("  2. Register it in <fg=cyan>config/livewire-form-builder.php</>:");
        $this->line("       'field_types' => ['{$type}' => \\App\\FormFields\\{$name}Field::class],");
        $this->line("  3. Customise the Blade stubs in <fg=cyan>resources/views/vendor/livewire-form-builder/fields/{$type}.blade.php</>.");

        return self::SUCCESS;
    }

    protected function classStub(string $name, string $type): string
    {
        return <<<PHP
<?php

namespace App\FormFields;

use Tivents\LivewireFormBuilder\Support\AbstractFieldType;

class {$name}Field extends AbstractFieldType
{
    public static function type(): string  { return '{$type}'; }
    public static function label(): string { return '{$name}'; }
    public static function icon(): string  { return 'heroicon-o-puzzle-piece'; }
    public static function group(): string { return 'inputs'; }

    public static function defaultConfig(): array
    {
        return array_merge(parent::defaultConfig(), [
            // Add your custom config keys here
        ]);
    }

    public function validationRules(array \$fieldConfig): array
    {
        return parent::validationRules(\$fieldConfig);
    }
}
PHP;
    }

    protected function fieldViewStub(string $type): string
    {
        return <<<BLADE
{{-- Field renderer for type: {$type} --}}
<div class="space-y-1">
    @if (!empty(\$field['label']))
        <label for="fa_{{ \$key }}" class="block text-sm font-medium text-gray-700">
            {{ \$field['label'] }}
            @if (!empty(\$field['required'])) <span class="text-red-500 ml-0.5">*</span> @endif
        </label>
    @endif

    {{-- TODO: Replace with your actual input HTML --}}
    <input
        type="text"
        id="fa_{{ \$key }}"
        wire:model.live="formData.{{ \$key }}"
        class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
    />

    @if (\$error ?? null)
        <p class="text-xs text-red-600">{{ \$error }}</p>
    @endif
</div>
BLADE;
    }

    protected function settingsViewStub(string $name): string
    {
        return <<<BLADE
{{-- Settings panel for field type: {$name} --}}
<div class="p-4 space-y-3">
    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{$name} Settings</h3>
    {{-- TODO: Add your type-specific settings inputs here --}}
    {{-- Example:
    <div>
        <label class="fa-label">My Option</label>
        <input type="text" wire:model.live="schema.{{ \$index }}.my_option" class="fa-input" />
    </div>
    --}}
</div>
BLADE;
    }

    protected function ensureDir(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
