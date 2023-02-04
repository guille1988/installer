<?php

namespace Felipetti\Installer;

use Felipetti\Installer\Commands\InstallationCommand;
use Felipetti\Installer\Commands\ParseCommand;
use Illuminate\Support\ServiceProvider;

class InstallerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
       //
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole())
            $this->commands(commands: [InstallationCommand::class, ParseCommand::class]);
    }
}

