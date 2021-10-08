<?php

namespace Bddy\Integrations;

use Bddy\Integrations\Console\Commands\ActionMakeCommand;
use Bddy\Integrations\Console\Commands\CastMakeCommand;
use Bddy\Integrations\Console\Commands\ChannelMakeCommand;
use Bddy\Integrations\Console\Commands\CommandMakeCommand;
use Bddy\Integrations\Console\Commands\ControllerMakeCommand;
use Bddy\Integrations\Console\Commands\EventMakeCommand;
use Bddy\Integrations\Console\Commands\ExceptionMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationAuthenticationStrategyMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationJobMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationJobMiddlewareMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationManifestMakeCommand;
use Bddy\Integrations\Console\Commands\IntegrationServiceProviderMakeCommand;
use Bddy\Integrations\Console\Commands\JobMakeCommand;
use Bddy\Integrations\Console\Commands\JsonApi\MakeAdapter;
use Bddy\Integrations\Console\Commands\JsonApi\MakeAuthorizer;
use Bddy\Integrations\Console\Commands\JsonApi\MakeContentNegotiator;
use Bddy\Integrations\Console\Commands\JsonApi\MakeResource;
use Bddy\Integrations\Console\Commands\JsonApi\MakeResourceProvider;
use Bddy\Integrations\Console\Commands\JsonApi\MakeSchema;
use Bddy\Integrations\Console\Commands\JsonApi\MakeValidators;
use Bddy\Integrations\Console\Commands\ListenerMakeCommand;
use Bddy\Integrations\Console\Commands\MailMakeCommand;
use Bddy\Integrations\Console\Commands\MiddlewareMakeCommand;
use Bddy\Integrations\Console\Commands\ModelMakeCommand;
use Bddy\Integrations\Console\Commands\NotificationMakeCommand;
use Bddy\Integrations\Console\Commands\ObserverMakeCommand;
use Bddy\Integrations\Console\Commands\PolicyMakeCommand;
use Bddy\Integrations\Console\Commands\ProviderMakeCommand;
use Bddy\Integrations\Console\Commands\RequestMakeCommand;
use Bddy\Integrations\Console\Commands\ResourceMakeCommand;
use Bddy\Integrations\Console\Commands\RuleMakeCommand;
use Bddy\Integrations\Contracts\EncryptSettingsService as EncryptSettingsServiceContract;
use Bddy\Integrations\Contracts\IntegrationModel as IntegrationContract;
use Bddy\Integrations\Contracts\IntegrationsRegistry as IntegrationsRegistryContract;
use Bddy\Integrations\Models\Integration;
use Bddy\Integrations\Services\EncryptSettingsService;
use Illuminate\Database\Eloquent\Relations\Relation;
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
	    $this->app->singleton(IntegrationsRegistryContract::class, IntegrationsRegistry::class);

	    $this->app->bind('integrations', function (Application $app) {
		    return $app->make(IntegrationsRegistryContract::class);
	    });

	    $this->app->bind(EncryptSettingsServiceContract::class, EncryptSettingsService::class);

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
			    __DIR__.'/../database/migrations/create_integrations_table.php' => $this->getMigrationFileName('create_integrations_table.php', $filesystem),
			    __DIR__.'/../database/migrations/create_failed_integration_jobs_table.php' => $this->getMigrationFileName('create_failed_integration_jobs_table.php', $filesystem)
		    ], 'migrations');
	    }

	    // Load and publish views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'anny');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/anny'),
        ]);

	    // Register model
	    $integrationModel = config('integrations.integrationModel') ?: Integration::class;
	    $this->app->bind(IntegrationContract::class, $integrationModel);

	    Integrations::useModel($integrationModel);

	    Relation::morphMap([
	    	'integrations' => config('integrations.integrationModel')
	    ], true);

	    // Commands
	    if ($this->app->runningInConsole()) {
		    $this->commands([
		    	ActionMakeCommand::class,
			    IntegrationMakeCommand::class,
			    IntegrationServiceProviderMakeCommand::class,
			    IntegrationManifestMakeCommand::class,
			    IntegrationJobMakeCommand::class,
			    IntegrationJobMiddlewareMakeCommand::class,
			    IntegrationAuthenticationStrategyMakeCommand::class,
			    CastMakeCommand::class,
			    ChannelMakeCommand::class,
			    CommandMakeCommand::class,
			    ControllerMakeCommand::class,
			    EventMakeCommand::class,
			    ExceptionMakeCommand::class,
//			    JobMakeCommand::class,
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

                // Json Api commands
                MakeResourceProvider::class,
                MakeAdapter::class,
                MakeAuthorizer::class,
                MakeContentNegotiator::class,
                MakeResource::class,
                MakeSchema::class,
                MakeValidators::class,
		    ]);
	    }
    }

	/**
	 * Returns existing migration file if found, else uses the current timestamp.
	 *
	 * Copied from
	 * @see https://github.com/spatie/laravel-permission/blob/master/src/PermissionServiceProvider.php
	 *
	 * @param string     $filename
	 * @param Filesystem $filesystem
	 *
	 * @return string
	 */
	protected function getMigrationFileName(string $filename, Filesystem $filesystem): string
	{
		$timestamp = date('Y_m_d_His');

		return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
			->flatMap(function ($path) use ($filename, $filesystem) {
				return $filesystem->glob($path.'*_'.$filename);
			})->push($this->app->databasePath()."/migrations/{$timestamp}_{$filename}")
			->first();
	}
}