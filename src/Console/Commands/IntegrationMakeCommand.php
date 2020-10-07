<?php


namespace Bddy\Integrations\Console\Commands;

use Illuminate\Support\Str;

class IntegrationMakeCommand extends \Illuminate\Console\GeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:integration';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Integration.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Integration';

	/**
	 * @return bool|null
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle()
	{
		if (parent::handle() === false) {
			return false;
		}

		$this->createServiceProvider();
	}

	/**
	 * Create a service provider for the integration.
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function createServiceProvider()
	{
		$provider = Str::studly($this->argument('name'));

		$this->call('make:integration:provider', [
			'name' => "{$provider}ServiceProvider",
		]);
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/stubs/integration.stub');
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
	 * Get the default namespace for the class.
	 *
	 * @param  string  $rootNamespace
	 * @return string
	 */
	protected function getDefaultNamespace($rootNamespace)
	{
		$integrationNamespace = \Illuminate\Support\Str::studly($this->argument('name'));

		return $rootNamespace.'\\Integrations\\'.$integrationNamespace;
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