<?php

namespace Tivents\LivewireFormBuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Tivents\LivewireFormBuilder\Commands\MakeFieldTypeCommand;
use Tivents\LivewireFormBuilder\Commands\PublishStubsCommand;
use Tivents\LivewireFormBuilder\Components\FormBuilder;
use Tivents\LivewireFormBuilder\Components\FormRenderer;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use Tivents\LivewireFormBuilder\Support\FieldRegistry;
use Tivents\LivewireFormBuilder\Support\FieldTypes\CheckboxField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\DateTimeField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\DividerField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\EmailField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\FileUploadField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\HeadingField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\HiddenField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\HintField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\HtmlField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\NumberField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\RadioField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\RepeaterField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\RowField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\SelectField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\TextareaField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\TextField;
use Tivents\LivewireFormBuilder\Support\FieldTypes\ToggleField;

class LivewireFormBuilderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/livewire-form-builder.php', 'livewire-form-builder');

        $this->app->singleton(FieldRegistry::class, function () {
            return new FieldRegistry();
        });

        // The FormRepository is bound to an interface so the host app can swap it.
        // The package ships no default implementation — the host must bind one,
        // or use the included EloquentFormRepository after running the stubs.
        if (config('livewire-form-builder.repository')) {
            $this->app->bind(
                FormRepositoryContract::class,
                config('livewire-form-builder.repository')
            );
        }
    }

    public function boot(): void
    {
        $this->registerFieldTypes();
        $this->registerLivewireComponents();
        $this->registerBladeComponents();
        $this->loadViews();
        $this->loadRoutes();
        $this->registerCommands();
        $this->publishAssets();
    }

    protected function registerFieldTypes(): void
    {
        $registry = $this->app->make(FieldRegistry::class);

        // Core input fields
        $registry->register('text',     TextField::class);
        $registry->register('email',    EmailField::class);
        $registry->register('textarea', TextareaField::class);
        $registry->register('number',   NumberField::class);
        $registry->register('select',   SelectField::class);
        $registry->register('checkbox', CheckboxField::class);
        $registry->register('radio',    RadioField::class);
        $registry->register('toggle',   ToggleField::class);
        $registry->register('datetime', DateTimeField::class);
        $registry->register('file',     FileUploadField::class);
        $registry->register('repeater', RepeaterField::class);
        $registry->register('hidden',   HiddenField::class);

        // Layout / structural fields
        $registry->register('row',     RowField::class);
        $registry->register('heading', HeadingField::class);
        $registry->register('hint',    HintField::class);
        $registry->register('html',    HtmlField::class);
        $registry->register('divider', DividerField::class);

        // Additional field types registered via config
        foreach (config('livewire-form-builder.field_types', []) as $type => $class) {
            $registry->register($type, $class);
        }
    }

    protected function registerLivewireComponents(): void
    {
        // Store class→name mapping so Livewire::test(ClassName) can reverse-lookup the tag name.
        Livewire::component('livewire-form-builder::builder',  FormBuilder::class);
        Livewire::component('livewire-form-builder::renderer', FormRenderer::class);

        // Livewire 4 does not check classComponents for namespaced (::) component names in
        // resolveClassComponentClassName — it only checks classNamespaces. Register a fallback
        // resolver so the name→class lookup succeeds for both tag rendering and Livewire::test().
        Livewire::resolveMissingComponent(function (string $name): ?string {
            return match ($name) {
                'livewire-form-builder::builder'  => FormBuilder::class,
                'livewire-form-builder::renderer' => FormRenderer::class,
                default                           => null,
            };
        });
    }

    protected function registerBladeComponents(): void
    {
        Blade::componentNamespace('Tivents\\LivewireFormBuilder\\View\\Components', 'livewire-form-builder');
    }

    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-form-builder');
    }

    protected function loadRoutes(): void
    {
        // Routes are optional — only load when builder_routes is enabled
        if (config('livewire-form-builder.builder_routes', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeFieldTypeCommand::class,
                PublishStubsCommand::class,
            ]);
        }
    }

    protected function publishAssets(): void
    {
        if ($this->app->runningInConsole()) {
            // Config
            $this->publishes([
                __DIR__ . '/../config/livewire-form-builder.php' => config_path('livewire-form-builder.php'),
            ], 'livewire-form-builder-config');

            // Views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/livewire-form-builder'),
            ], 'livewire-form-builder-views');

            // Stubs (migration + model templates for the host app)
            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/livewire-form-builder'),
            ], 'livewire-form-builder-stubs');
        }
    }
}
