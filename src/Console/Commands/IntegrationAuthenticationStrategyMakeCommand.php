<?php


namespace Anny\Integrations\Console\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class IntegrationAuthenticationStrategyMakeCommand extends AbstractGeneratorCommand
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:integration:authentication-strategy';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new Integration authentication strategy.';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'IntegrationAuthenticationStrategy';


	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
	    if($this->option('oauth2')) {
            return $this->resolveStubPath('/stubs/integration-oauth2-authentication-strategy.stub');
        }

        if($this->option('access_token')) {
            return $this->resolveStubPath('/stubs/integration-access-token-authentication-strategy.stub');
        }
		return $this->resolveStubPath('/stubs/integration-authentication-strategy.stub');
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
		return [
            ['oauth2', 'oauth2', InputOption::VALUE_NONE, 'Make an oauth2 authentication strategy.'],
            ['access_token', 'at', InputOption::VALUE_NONE, 'Make an access token authentication strategy'],
        ];
	}
}