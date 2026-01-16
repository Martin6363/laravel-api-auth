<?php

namespace Martin6363\ApiAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class InstallApiAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-auth:install
                            {--force : Overwrite existing configuration file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel API Auth package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Installing Laravel API Auth Package...');

        $this->publishConfig();
        $this->publishLanguages();

        $this->checkSanctum();

        if ($this->confirm('Do you want to publish controllers and services to customize the logic?', true)) {
            $this->info('Publishing internal logic...');
            $this->call('vendor:publish', ['--tag' => 'api-auth-logic', '--force' => $this->option('force')]);

            $this->fixPublishedNamespaces();
        }

        $this->runMigrations();

        $this->info('✅ Installation complete!');
        return CommandAlias::SUCCESS;
    }

    protected function fixPublishedNamespaces(): void
    {
        $this->info('🔧 Adjusting namespaces in app/ directory...');

        $directories = [
            app_path('Http/Controllers/ApiAuth'),
            app_path('Http/Requests/ApiAuth'),
            app_path('Services/ApiAuth'),
        ];

        foreach ($directories as $directory) {
            if (!File::isDirectory($directory)) {
                continue;
            }

            foreach (File::allFiles($directory) as $file) {
                $content = File::get($file);

                $content = str_replace(array('Martin6363\\ApiAuth\\Http\\Controllers', 'Martin6363\\ApiAuth\\Http\\Requests', 'Martin6363\\ApiAuth\\Services', 'use Martin6363\\ApiAuth\\'),
                    array('App\\Http\\Controllers\\ApiAuth', 'App\\Http\\Requests\\ApiAuth', 'App\\Services\\ApiAuth', 'use App\\'), $content);

                File::put($file, $content);
            }
        }
        $this->info('   ✓ Namespaces updated to App\... standard.');
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('📋 Publishing configuration file...');

        $this->call('vendor:publish', [
            '--provider' => 'Vendor\ApiAuth\Providers\ApiAuthServiceProvider',
            '--tag' => 'api-auth-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('   ✓ Configuration file published to config/api-auth.php');
    }

    /**
     * Publish language files.
     */
    protected function publishLanguages(): void
    {
        $this->info('🌐 Publishing language files...');

        $this->call('vendor:publish', [
            '--provider' => 'Vendor\ApiAuth\Providers\ApiAuthServiceProvider',
            '--tag' => 'api-auth-lang',
            '--force' => $this->option('force'),
        ]);

        $this->info('   ✓ Language files published to lang/en/auth.php');
    }

    /**
     * Check if Laravel Sanctum is properly installed.
     */
    protected function checkSanctum(): void
    {
        if (!class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
            $this->info('📦 Installing Laravel Sanctum...');
            $this->call('install:api');
        }
    }

    /**
     * Run migrations if requested.
     */
    protected function runMigrations(): void
    {
        if ($this->confirm('Do you want to run migrations now?', true)) {
            $this->info('🔄 Running migrations...');
            $this->call('migrate');
            $this->info('   ✓ Migrations completed');
        }
    }
}