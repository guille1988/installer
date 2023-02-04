<?php

namespace Felipetti\Installer\Commands;

use JetBrains\PhpStorm\NoReturn;
use Illuminate\Console\Command;

abstract class BaseCommand extends Command
{
    // Contains the path of the folder which has the package to install
    protected string $installationPath;

    /**
     * Creates the variables needed to pass on to child classes
     */
    public function __construct()
    {
        parent::__construct();
        $this->installationPath = base_path('installation');
    }

    /**
     * Error message to handle in the command
     *
     * @param string $message
     * @return void
     */
    #[NoReturn] public function throwError(string $message): void
    {
        $this->components->error($message);
        exit(1);
    }

    /**
     * Command base functionality to implement specifically in every child class
     *
     * @return void
     */
    abstract public function handle(): void;
}
