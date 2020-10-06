<?php

namespace Bddy\Integrations\Tests;


use Bddy\Integrations\IntegrationsManager;
use Bddy\Integrations\IntegrationsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

	/**
	 * Register package providers
	 * @param \Illuminate\Foundation\Application $app
	 *
	 * @return array|string[]
	 */
	protected function getPackageProviders($app)
	{
		return [
			IntegrationsServiceProvider::class
		];
	}

	/**
	 * Register Facade
	 * @param \Illuminate\Foundation\Application $app
	 *
	 * @return array|string[]
	 */
	protected function getPackageAliases($app)
	{
		return [
		];
	}

	/**
	 * Get pipelines manager from container.
	 * @return IntegrationsManager
	 */
	protected function getManager()
	{
		return $this->app->make('integrations');
	}

}
