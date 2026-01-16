<?php

namespace Vendor\ApiAuth\Console\Commands;

use Illuminate\Console\Command;

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
        $this->newLine();

        // Publish configuration
        $this->publishConfig();

        // Publish language files
        $this->publishLanguages();

        // Check Sanctum installation
        $this->checkSanctum();

        // Run migrations if needed
        $this->runMigrations();

        $this->newLine();
        $this->info('✅ Laravel API Auth Package installed successfully!');
        $this->newLine();
        $this->line('📝 Next steps:');
        $this->line('   1. Review the configuration file: config/api-auth.php');
        $this->line('   2. Ensure your User model uses HasApiTokens trait from Laravel Sanctum');
        $this->line('   3. Configure your email settings for password reset and verification');
        $this->line('   4. Customize routes and middleware as needed');
        $this->newLine();

        return Command::SUCCESS;
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
        $this->info('🔍 Checking Laravel Sanctum installation...');

        if (! class_exists(\Laravel\Sanctum\SanctumServiceProvider::class)) {
            $this->warn('   ⚠ Laravel Sanctum is not installed.');
            $this->line('   Run: composer require laravel/sanctum');
            $this->line('   Then: php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"');
            $this->line('   And: php artisan migrate');
            $this->newLine();
        } else {
            $this->info('   ✓ Laravel Sanctum is installed');
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