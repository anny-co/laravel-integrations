<?php


namespace Bddy\Integrations\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

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

		if($this->option('oauth2')) {
		    $this->createOAuth2AuthenticationStrategy($name);
        }

        if($this->option('access_token')) {
            $this->createAccessTokenAuthenticationStrategy($name);
        }

        return true;
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

    protected function createOAuth2AuthenticationStrategy(string $name)
    {
        $this->call('make:integration:authentication-strategy', [
            'name' => "{$name}OAuth2Authentication",
            '--oauth2' => 'true'
        ]);
    }

    protected function createAccessTokenAuthenticationStrategy(string $name)
    {
        $this->call('make:integration:authentication-strategy', [
            'name' => "{$name}AccessTokenAuthentication",
            '--access_token' => 'true'
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
        $name = Str::of($this->argument('name'))->studly();

        // Replace strategies
        $strategies = "";
        if($this->option('oauth2')){
            $strategies .= "new ${name}OAuth2Authentication,";
        }

        if($this->option('oauth2') && $this->option('access_token')) {
            $strategies .= "\n\t\t\t";
        }

        if($this->option('access_token')){
            $strategies .= "new ${name}AccessTokenAuthentication,";
        }

        $stub = str_replace('{{ strategies }}', $strategies, $stub);

        // Replace key
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
		return [
            ['oauth2', 'oauth2', InputOption::VALUE_NONE, 'Include oauth2 authentication strategy for this integration..'],
            ['access_token', 'at', InputOption::VALUE_NONE, 'Include oauth2 authentication strategy for this integration..'],
        ];
	}


}