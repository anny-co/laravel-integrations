<?php


namespace Anny\Integrations\Console\Commands;

class IntegrationServiceProviderMakeCommand extends AbstractGeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:integration:provider';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Integration service provider.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'IntegrationServiceProvider';

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
		return $this->resolveStubPath('/stubs/integration-provider.stub');
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
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}
}