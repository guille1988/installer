# Installation and usage instructions:

## What it does:

This package allows you to make a self installation of auto-generated packages on Laravel projects.

## Installation:

```bash
    composer require felipetti/installer
```

## Usage instructions:

### STEP 1: Prepare installation

Make a folder with the name 'installation' on the root directory of the project, 
and place all files to install within that directory. The package will install all of them 
into the Laravel project. Make sure that the extension of the files must be '*.stub', to avoid any errors.

**Example:**

If the installation requires to overwrite file 'routes/api.php' of the project, place the new file in:
'installation/routes/api.stub', and the package will install it. 

So, in conclusion, we must build a Laravel tree directory inside installation folder, to make this work

### STEP 2: Parsing directory

Once the installation folder it's finished, enter the following command:

```bash
    php artisan parse:directory
```
This command will generate a file named 'config/installer.php'. This file will approximately have this appearance:

```php
    <?php

    return [
    
        /*
        |--------------------------------------------------------------------------
        | Installer configuration
        |--------------------------------------------------------------------------
        |
        | This option sets the type of installation you want to produce. There is:
        | replace, append and aggregate. By default, it will be set to replace.
        |
        */
    
        'app/Broadcasting/PrivateNotificationChannel.stub' => 'replace',
        'app/Contracts/EmailNotifications/EmailNotificationsBase.stub' => 'append',
        'app/Events/NotificationsEvent.stub' => 'aggregate'
    ];
```
It will be an array of all the files inside installation folder as key and installation mode as value.
There are three modes of installation of a file:

1. 'replace': It replaces completely the given file for the project one or if it not exists, it creates a new one.
2. 'append': Same as replace but the content will append at the bottom of the project one.
3. 'aggregate': This is a special one, it is used to append methods at the bottom of a class,
                being this class, the only one in the file.

**IMPORTANT** 
In append or aggregate mode only put in the file what you want to add, put only the complete
file in replace mode.

You can set in this configuration file, the installation mode of every file. Take in count that, 
if the file extension is not '*.stub', you will be able to generate the configuration file to see
the error, but not the further process of installation that comes next.
You can parse the directory as many times that you want, the package will automatically update config
file for you.

### STEP 3: Make installation

Once ths config file it's generated, and all files have the appropriated mode and extension, you can run:

```bash
    php artisan make installation
```

The installation process of copying files will start, and they will be merged on the Laravel project.
You can repeat the parse ----> install process as many times you want.
In the final stage of the installation command, you can delete the installation folder and the
configuration file, if you wish to. 

## Security:

If you discover any security-related issues, please e-mail me to the one above instead of using the issue tracker.

## License:

The MIT License (MIT).
