<?php

namespace Bddy\Integrations;

use Bddy\Integrations\Console\Commands\IntegrationMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationServiceProviderMakeCommand;
use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Contracts\IntegrationsManager as IntegrationsManagerContract;
use Bddy\Integrations\Models\Integration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class IntegrationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    	// Register bindings
	    $this->app->singleton(IntegrationsManagerContract::class, IntegrationsManager::class);

	    $this->app->bind('integrations', function (Application $app) {
		    return $app->make(IntegrationsManagerContract::class);
	    });

	    // Merge config
	    $this->mergeConfigFrom(
		    __DIR__.'/../config/integrations.php', 'integrations'
	    );

    }

	/**
	 * Bootstrap services.
	 *
	 * @param Filesystem $filesystem
	 *
	 * @return void
	 */
    public function boot(Filesystem $filesystem)
    {
	    // Publish migrations
	    if(function_exists('config_path')){
		    $this->publishes([
			    __DIR__.'/../config/integrations.php' => config_path('integrations.php')
		    ], 'config');

		    // Publish migrations
		    $this->publishes([
			    __DIR__.'/../database/migrations/create_integrations_table.php' => $this->getMigrationFileName($filesystem)
		    ], 'migrations');
	    }

	    // Register model
	    $integrationModel = config('integrations.integrationModel') ?: Integration::class;
	    $this->app->bind(IntegrationModel::class, $integrationModel);

	    // Commands
	    if ($this->app->runningInConsole()) {
		    $this->commands([
			    IntegrationMakeCommand::class,
			    IntegrationServiceProviderMakeCommand::class
		    ]);
	    }
    }

	/**
	 * Returns existing migration file if found, else uses the current timestamp.
	 *
	 * Copied from
	 * @see https://github.com/spatie/laravel-permission/blob/master/src/PermissionServiceProvider.php
	 * @param Filesystem $filesystem
	 * @return string
	 */
	protected function getMigrationFileName(Filesystem $filesystem): string
	{
		$timestamp = date('Y_m_d_His');

		return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
			->flatMap(function ($path) use ($filesystem) {
				return $filesystem->glob($path.'*_create_integrations_tables.php');
			})->push($this->app->databasePath()."/migrations/{$timestamp}_create_integrations_tables.php")
			->first();
	}
}
