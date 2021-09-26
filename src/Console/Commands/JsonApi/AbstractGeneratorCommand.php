<?php

namespace Bddy\Integrations\Console\Commands\JsonApi;

use CloudCreativity\LaravelJsonApi\Console\Commands\AbstractGeneratorCommand as BaseAbstractGeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class AbstractGeneratorCommand extends BaseAbstractGeneratorCommand
{

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['integration', InputArgument::REQUIRED, 'The integration to use'],
            ['resource', InputArgument::REQUIRED, "The resource for which a {$this->type} class will be generated."],
            ['api', InputArgument::OPTIONAL, "The API that the resource belongs to."],
        ];
    }

    /**
     * @param string $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $integrationNamespace =  Str::studly($this->argument('integration'));
        $resource = $this->getResourceName();
        $classified = \CloudCreativity\LaravelJsonApi\Utils\Str::classify($resource);
        $api = \CloudCreativity\LaravelJsonApi\Utils\Str::classify($this->getApiName());

        return "${rootNamespace}\\Integrations\\${integrationNamespace}\\JsonApi\\${api}\\${classified}";
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
}