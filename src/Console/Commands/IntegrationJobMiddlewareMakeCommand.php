<?php


namespace Anny\Integrations\Console\Commands;

class IntegrationJobMiddlewareMakeCommand extends \Illuminate\Console\GeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:integration:job-middleware';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new integration job middleware.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'IntegrationJobMiddleware';

	/**
	 * @return bool|null
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle()
	{
		if (parent::handle() === false) {
			return false;
		}
	}

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/stubs/integration-job-middleware.stub');
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
		$integrationNamespace =  \Illuminate\Support\Str::studly($this->argument('name'));
		$integrationNamespace = str_replace('Middleware', '', $integrationNamespace);

		return $rootNamespace.'\\Integrations\\'.$integrationNamespace . '\\Jobs\\Middleware';
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