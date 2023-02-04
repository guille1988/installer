<?php

namespace Felipetti\Installer\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

class ParseCommand extends BaseCommand
{
    // Command that triggers installation
    protected $signature = 'parse:directory';

    // Description of command
    protected $description = 'Parse folder to install';

    // Message of the array of the config file
    private string $message ='
    /*
    |--------------------------------------------------------------------------
    | Installer configuration
    |--------------------------------------------------------------------------
    |
    | This option sets the type of installation you want to produce. There is:
    | replace, append and aggregate. By default, it will be set to replace.
    |
    */' . PHP_EOL;

    /**
     * Generates the data of the array of the config file
     *
     * @param Collection $paths
     * @return string
     */
    public function makeData(Collection $paths): string
    {
        $data = '';
        $paths->each(function($path) use (&$data)
        {
            $data .= "\t" . "'$path' => " . "'replace'," . PHP_EOL;
        });

        return $data;
    }

    /**
     * Generates the config file
     *
     * @param Collection $paths
     * @return string
     */
    public function buildFile(Collection $paths): string
    {
        $start = '<?php' . PHP_EOL . PHP_EOL . 'return [' . PHP_EOL . $this->message . PHP_EOL;
        $data = substr_replace($this->makeData($paths), '', -2);
        $end = PHP_EOL . '];';

        return $start . $data . $end;
    }

    /**
     * Command base functionality
     *
     * @return void
     */
    public function handle(): void
    {
        if(File::isDirectory($this->installationPath) && !File::isEmptyDirectory($this->installationPath))
        {
            $paths = collect(File::allFiles($this->installationPath))
                ->map(fn($file) => str_replace($this->installationPath . '/', '', $file->getPathName()));

            file_put_contents(config_path('installer.php'), $this->buildFile($paths));

            $this->components->info('Config file installer.php created successfully');
        }
        else
            $this->throwError("Installation directory doesn't exist or is empty");
    }
}
