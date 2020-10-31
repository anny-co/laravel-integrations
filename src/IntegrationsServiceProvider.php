<?php

namespace Bddy\Integrations;

use Bddy\Integrations\Console\Commands\ActionMakeCommand;
use Bddy\Integrations\Console\Commands\CastMakeCommand;
use Bddy\Integrations\Console\Commands\ChannelMakeCommand;
use Bddy\Integrations\Console\Commands\CommandMakeCommand;
use Bddy\Integrations\Console\Commands\ControllerMakeCommand;
use Bddy\Integrations\Console\Commands\EventMakeCommand;
use Bddy\Integrations\Console\Commands\ExceptionMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationServiceProviderMakeCommand;
use Bddy\Integrations\Console\Commands\JobMakeCommand;
use Bddy\Integrations\Console\Commands\ListenerMakeCommand;
use Bddy\Integrations\Console\Commands\MailMakeCommand;
use Bddy\Integrations\Console\Commands\MiddlewareMakeCommand;
use Bddy\Integrations\Console\Commands\MigrationMakeCommand;
use Bddy\Integrations\Console\Commands\ModelMakeCommand;
use Bddy\Integrations\Console\Commands\NotificationMakeCommand;
use Bddy\Integrations\Console\Commands\ObserverMakeCommand;
use Bddy\Integrations\Console\Commands\PolicyMakeCommand;
use Bddy\Integrations\Console\Commands\ProviderMakeCommand;
use Bddy\Integrations\Console\Commands\RequestMakeCommand;
use Bddy\Integrations\Console\Commands\ResourceMakeCommand;
use Bddy\Integrations\Console\Commands\RuleMakeCommand;
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
		    	ActionMakeCommand::class,
			    IntegrationMakeCommand::class,
			    IntegrationServiceProviderMakeCommand::class,
			    CastMakeCommand::class,
			    ChannelMakeCommand::class,
			    CommandMakeCommand::class,
			    ControllerMakeCommand::class,
			    EventMakeCommand::class,
			    ExceptionMakeCommand::class,
			    JobMakeCommand::class,
			    ListenerMakeCommand::class,
			    MailMakeCommand::class,
			    MiddlewareMakeCommand::class,
			    ModelMakeCommand::class,
			    NotificationMakeCommand::class,
			    ObserverMakeCommand::class,
			    PolicyMakeCommand::class,
			    ProviderMakeCommand::class,
			    RequestMakeCommand::class,
			    ResourceMakeCommand::class,
			    RuleMakeCommand::class,
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
