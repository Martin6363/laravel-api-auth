<?php

namespace Martin6363\ApiAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as CommandAlias;

class UninstallApiAuthCommand extends Command
{
    protected $signature = 'api-auth:uninstall';

    protected $description = 'Remove the Laravel API Auth package files and configuration';

    public function handle(): int
    {
        $this->warn('⚠️ This will delete all published Api-Auth files (Controllers, Services, Config, etc.)');

        if (!$this->confirm('Are you sure you want to proceed?', false)) {
            $this->info('Uninstall cancelled.');
            return CommandAlias::SUCCESS;
        }

        $this->info('🧹 Removing Laravel API Auth components...');

        // 1. Remove Config
        $this->removeFile(config_path('api-auth.php'), 'Configuration file');

        // 2. Remove Published Directories
        $directories = [
            app_path('Http/Controllers/ApiAuth'),
            app_path('Services/ApiAuth'),
            app_path('Http/Requests/ApiAuth'),
            app_path('Http/Resources/ApiAuth'),
            app_path('Notifications/ApiAuth'),
            resource_path('views/emails/api-auth'),
        ];

        foreach ($directories as $directory) {
            $this->removeDirectory($directory);
        }

        // 3. Remove Language files (Be careful here not to delete main auth.php if customized)
        // Check if it's the package's translation before deleting
        $langPath = lang_path('en/auth.php');
        if (File::exists($langPath)) {
            $this->info('   - Note: Please check lang/en/auth.php manually if you wish to revert it.');
        }

        $this->info('✅ Cleanup complete! You can now run: composer remove martin6363/laravel-api-auth');

        return CommandAlias::SUCCESS;
    }

    protected function removeFile(string $path, string $label): void
    {
        if (File::exists($path)) {
            File::delete($path);
            $this->info("   ✓ $label removed.");
        }
    }

    protected function removeDirectory(string $path): void
    {
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
            $this->info("   ✓ Directory [" . basename($path) . "] removed.");
        }
    }
}