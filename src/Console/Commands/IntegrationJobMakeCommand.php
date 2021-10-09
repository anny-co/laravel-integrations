<?php


namespace Anny\Integrations\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class IntegrationJobMakeCommand extends AbstractGeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:integration:job';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new integration job.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'IntegrationJob';

	/**
	 * @return bool|null
	 * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
	 */
	public function handle()
	{
		if (parent::handle() === false) {
			return false;
		}
		// Check if we need to create the middleware
        $middleware = $this->option('middleware');

		if($middleware) {
            $this->call('make:integration:job-middleware', [
                'name' => $this->getMiddlewareName()
            ]);
        }
	}

    /**
     * Returns integration name cleared from "Job" suffix.
     *
     * @return \Illuminate\Support\Stringable
     */
	protected function getClearedName() {
	    return Str::of($this->argument('name'))->studly()->replace('Job', '');
    }

    /**
     * Returns middleware name class.
     *
     * @return string
     */
    protected function getMiddlewareName() {
        $name = $this->getClearedName();

	    return "{$name}Middleware";
    }

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/stubs/integration-job.stub');
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
		$integrationNamespace = str_replace('Job', '', $integrationNamespace);

		return $rootNamespace.'\\Integrations\\'.$integrationNamespace.'\\Jobs';
	}

	protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        // Replace middleware
        $useMiddleware = $this->option('middleware');
        $middleware = "";
        $middlewareImport = "";
        if($useMiddleware) {
            $name = $this->getMiddlewareName();
            $middleware = "new {$name}";

            $rootNamespace = trim($this->rootNamespace(), '\\');
            $namespace = $this->getDefaultNamespace($rootNamespace) ."\\Middleware\\${name}";
            $middlewareImport = "use ${namespace};";
        }

        $stub = str_replace(['DummyMiddleware', '{{ middleware }}', '{{middleware}}'], $middleware, $stub);
        $stub = str_replace(['{{ middlewareImport }}', '{{ middleware_import }}'], $middlewareImport, $stub);

        return $stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['middleware', 'm', InputOption::VALUE_NONE, 'Create a middleware for the job.'],
        ];
    }
}