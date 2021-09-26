<?php


namespace Bddy\Integrations\Console\Commands;

use Illuminate\Support\Str;

class IntegrationManifestMakeCommand extends AbstractGeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:integration:manifest';

	/**
	 * The console command description.
	 *
	 * @var stringfeat: added make commands for json-api
	 */
	protected $description = 'Create a new integration manifest.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'IntegrationManifest';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/stubs/integration-manifest.stub');
	}


	/**
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		$integrationNamespace =  \Illuminate\Support\Str::studly($this->argument('name'));
		$integrationNamespace = str_replace('Manifest', '', $integrationNamespace);

		return $rootNamespace.'\\Integrations\\'.$integrationNamespace;
	}

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $title = Str::of($this->argument('name'))->studly()->replace('Manifest', '');

        return str_replace('{{ title }}', $title, $stub);
    }

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}
}