<?php

namespace Tivents\LivewireFormBuilder\Commands;

use Illuminate\Console\Command;

class PublishStubsCommand extends Command
{
    protected $signature   = 'livewire-form-builder:publish-stubs {--force : Overwrite existing files}';
    protected $description = 'Publish migration stubs and a sample repository to your application';

    public function handle(): int
    {
        $force    = $this->option('force');
        $stubDir  = __DIR__ . '/../../stubs';
        $files    = [
            "{$stubDir}/LivewireFormBuilderRepository.php.stub" => app_path('Repositories/LivewireFormBuilderRepository.php'),
        ];

        foreach ($files as $source => $dest) {
            if (file_exists($dest) && !$force) {
                $this->warn("Already exists (use --force to overwrite): {$dest}");
                continue;
            }
            $dir = dirname($dest);
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            copy($source, $dest);
            $this->info("Published: {$dest}");
        }

        $this->newLine();
        $this->line('Next steps:');
        $this->line('  1. Create your own migration for the forms + submissions tables.');
        $this->line('  2. Bind the repository in <fg=cyan>AppServiceProvider::register()</>:');
        $this->line('       $this->app->bind(\\Tivents\\LivewireFormBuilder\\Contracts\\FormRepositoryContract::class,');
        $this->line('                        \\App\\Repositories\\LivewireFormBuilderRepository::class);');
        $this->line('     — or set it in config/livewire-form-builder.php:');
        $this->line("       'repository' => \\App\\Repositories\\LivewireFormBuilderRepository::class,");

        return self::SUCCESS;
    }
}
