<?php

namespace Martin6363\ApiAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Martin6363\ApiAuth\Providers\ApiAuthServiceProvider;
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

        if ($this->confirm('Do you want to publish controllers and services to customize the logic?', false)) {
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

        $replacements = $this->getNamespaceReplacements();

        $directories = [
            app_path('Http/Controllers/ApiAuth/v1'),
            app_path('Http/Requests/ApiAuth/v1'),
            app_path('Services/ApiAuth/v1'),
            app_path('Http/Resources/ApiAuth/v1'),
            app_path('Notifications/ApiAuth'),
        ];

        foreach ($directories as $directory) {
            if (! File::isDirectory($directory)) {
                continue;
            }

            foreach (File::allFiles($directory) as $file) {
                $content = File::get($file);

                // Replace namespaces
                $content = str_replace(
                    array_keys($replacements),
                    array_values($replacements),
                    $content
                );

                File::put($file, $content);
            }
        }
        $this->info('   ✓ Namespaces updated to App\... standard.');
    }

    protected function getNamespaceReplacements(): array
    {
        return [
            // Original Namespace => New Namespace
            'Martin6363\\ApiAuth\\Http\\Controllers\\v1' => 'App\\Http\\Controllers\\ApiAuth\\v1',
            'Martin6363\\ApiAuth\\Http\\Requests\\v1' => 'App\\Http\\Requests\\ApiAuth\\v1',
            'Martin6363\\ApiAuth\\Services\\v1' => 'App\\Services\\ApiAuth\\v1',
            'Martin6363\\ApiAuth\\Http\\Resources\\v1' => 'App\\Http\\Resources\\ApiAuth\\v1',
            'Martin6363\\ApiAuth\\Notifications' => 'App\\Notifications\\ApiAuth',

            // For use statements
            'use Martin6363\\ApiAuth\\Http\\Controllers\\v1' => 'use App\\Http\\Controllers\\ApiAuth\\v1',
            'use Martin6363\\ApiAuth\\Http\\Requests\\v1' => 'use App\\Http\\Requests\\ApiAuth\\v1',
            'use Martin6363\\ApiAuth\\Services\\v1' => 'use App\\Services\\ApiAuth\\v1',
            'use Martin6363\\ApiAuth\\Http\\Resources\\v1' => 'use App\\Http\\Resources\\ApiAuth\\v1',
            'use Martin6363\\ApiAuth\\' => 'use App\\',
        ];
    }

    /**
     * Publish the configuration file.
     */
    protected function publishConfig(): void
    {
        $this->info('📋 Publishing configuration file...');

        $this->call('vendor:publish', [
            '--provider' => ApiAuthServiceProvider::class,
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
            '--provider' => ApiAuthServiceProvider::class,
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
        if (! class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
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
