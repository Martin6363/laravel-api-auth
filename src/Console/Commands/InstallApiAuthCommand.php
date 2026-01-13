<?php

namespace Vendor\ApiAuth\Console\Commands;

use Illuminate\Console\Command;

class InstallApiAuthCommand extends Command
{
    protected $signature = 'api-auth:install';
    protected $description = 'Install the Professional API Auth Package';

    public function handle()
    {
        $this->info('Installing API Auth Package...');

        // Publishing the config
        $this->publishConfig();

        // 2. Sanctum setup (optional)
        if ($this->confirm('Do you want to run migrations?', true)) {
            $this->call('migrate');
        }

        $this->info('API Auth Package installed successfully. 😊❤️');
    }

    protected function publishConfig()
    {
        $this->call('vendor:publish', [
            '--provider' => "Vendor\ApiAuth\Providers\ApiAuthServiceProvider",
            '--tag' => 'api-auth-config'
        ]);
    }
}