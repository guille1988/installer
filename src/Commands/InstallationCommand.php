<?php

namespace Felipetti\Installer\Commands;

use Illuminate\Support\Facades\File;
use Exception;
use Error;

class InstallationCommand extends BaseCommand
{
    // Command that triggers installation
    protected $signature = 'make:installation';

    // Description of command
    protected $description = 'Installation of parsed package';

    // Available modes of installation
    private array $modes = ['replace', 'append', 'aggregate'];

    /**
     * Installation message on copying files
     *
     * @param string $destinationPath
     * @return void
     */
    public function fileCopiedMessage(string $destinationPath): void
    {
        $this->components->task('Installing --------> ' . $destinationPath);
        usleep(100000);
    }

    /**
     * Validation on installation mode
     *
     * @param string $mode
     * @return void
     */
    public function validateMode(string $mode): void
    {
        if (!in_array($mode, $this->modes))
            $this->throwError("Mode of installation: '$mode' doesn't exist, available modes are: " .
                implode(', ', $this->modes));
    }

    /**
     * Validation of extension of the file
     *
     * @param string $file
     * @return void
     */
    public function validateExtension(string $file): void
    {
        if(!str_ends_with($file, '.stub'))
            $this->throwError("Installation can only be performed with '*.stub' file extensions to avoid errors");
    }

    /**
     * @param string $file
     * @param string $mode
     * @return void
     */
    public function install(string $file, string $mode): void
    {
        try
        {
            $this->validateMode($mode);
            $this->validateExtension($file);

            $stubData = file_get_contents("$this->installationPath/" . $file);
            $destinationPath = str_replace('.stub', '.php', base_path($file));

            $destinationDirectory = str_replace(strrchr($destinationPath, '/'), '', $destinationPath);
            File::ensureDirectoryExists($destinationDirectory, 0777);

            if($mode == 'aggregate')
            {
                $fileData = file_get_contents($destinationPath);
                $modifiedData = str_replace(strrchr($fileData, '}'), $stubData, $fileData);

                file_put_contents($destinationPath, $modifiedData);
            }
            else
                file_put_contents($destinationPath, $stubData, $mode == 'append'? FILE_APPEND : 0);

            chmod($destinationPath, 0777);
            $this->fileCopiedMessage($destinationPath);
        }
        catch (Exception|Error $exception)
        {
            $this->throwError($exception->getMessage());
        }
    }

    /**
     * Command base functionality
     *
     * @return void
     */
    public function handle(): void
    {
    	if($this->directoryExistsAndHasAnyFile($this->installationPath))
        {
	     collect(config('installer'))->each(fn($mode, $file) => $this->install($file, $mode));

	     echo PHP_EOL;
	     $this->info('  File copy process finished successfully');

	     if($this->confirm('Do you wish to delete installation folder?'))
		File::deleteDirectory($this->installationPath);

	    if($this->confirm('Do you wish to delete config file?'))
		File::delete(config_path('installer.php'));

	    $this->components->info('Installation completed successfully');
        }
        else
            $this->throwError("Installation directory doesn't exist or has any files inside it");
    }
}
