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

		$name = Str::studly($this->argument('name'));
		$this->createServiceProvider($name);
		$this->createManifest($name);
		$this->createJob($name);
	}

	/**
	 * Create a service provider for the integration.
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	protected function createServiceProvider(string $name)
	{
		$this->call('make:integration:provider', [
			'name' => "{$name}ServiceProvider",
		]);
	}

    /**
     * Create a manifest for the integration.
     *
     * @param string $name
     */
	protected function createManifest(string $name) {
        $this->call('make:integration:manifest', [
            'name' => "{$name}Manifest",
        ]);
    }

    /**
     * Create central job for integration.
     *
     * @param string $name
     */
    protected function createJob(string $name) {
        $this->call('make:integration:job', [
            'name' => "{$name}Job",
            '--middleware' => 'true'
        ]);
    }

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/stubs/integration-manager.stub');
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
        $key = Str::of($this->argument('name'))->studly()->replace('Job', '')->snake('-');

        return str_replace('{{ key }}', $key, $stub);
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