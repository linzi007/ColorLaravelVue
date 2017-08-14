<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\File;
use Summerblue\Generator\Commands\ScaffoldMakeCommand;

class ColorScaffoldMakeCommand extends ScaffoldMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:color';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create color scaffold';

    protected function makeViewLayout()
    {

    }

    protected function makeViews()
    {

    }

}
