<?php

namespace Tivents\LivewireFormBuilder\Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Tivents\LivewireFormBuilder\Contracts\FormRepositoryContract;
use Tivents\LivewireFormBuilder\LivewireFormBuilderServiceProvider;
use Tivents\LivewireFormBuilder\Tests\Fakes\FakeFormRepository;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        FakeFormRepository::reset();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            LivewireFormBuilderServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app->bind(FormRepositoryContract::class, FakeFormRepository::class);
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['view']->addNamespace('livewire-form-builder', __DIR__ . '/../resources/views');
    }
}
