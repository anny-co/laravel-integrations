<?php

namespace Anny\Integrations\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

abstract class AbstractGeneratorCommand extends GeneratorCommand
{

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $integrationNamespace = Str::studly($this->argument('integration'));

        return $rootNamespace.'\\Integrations\\'.$integrationNamespace;
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['integration', InputArgument::REQUIRED, 'The integration to use'],
            ['name', InputArgument::REQUIRED, 'The name of the class'],
        ];
    }
}