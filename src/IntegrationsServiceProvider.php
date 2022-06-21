<?php

namespace Anny\Integrations;

use Anny\Integrations\Console\Commands\ActionMakeCommand;
use Anny\Integrations\Console\Commands\CastMakeCommand;
use Anny\Integrations\Console\Commands\ChannelMakeCommand;
use Anny\Integrations\Console\Commands\CommandMakeCommand;
use Anny\Integrations\Console\Commands\ControllerMakeCommand;
use Anny\Integrations\Console\Commands\EventMakeCommand;
use Anny\Integrations\Console\Commands\ExceptionMakeCommand;
use Anny\Integrations\Console\Commands\IntegrationAuthenticationStrategyMakeCommand;
use Anny\Integrations\Console\Commands\IntegrationJobMakeCommand;
use Anny\Integrations\Console\Commands\IntegrationJobMiddlewareMakeCommand;
use Anny\Integrations\Console\Commands\IntegrationMakeCommand;
use Anny\Integrations\Console\Commands\IntegrationManifestMakeCommand;
use Anny\Integrations\Console\Commands\IntegrationServiceProviderMakeCommand;
use Anny\Integrations\Console\Commands\JobMakeCommand;
use Anny\Integrations\Console\Commands\JsonApi\MakeAdapter;
use Anny\Integrations\Console\Commands\JsonApi\MakeAuthorizer;
use Anny\Integrations\Console\Commands\JsonApi\MakeContentNegotiator;
use Anny\Integrations\Console\Commands\JsonApi\MakeResource;
use Anny\Integrations\Console\Commands\JsonApi\MakeResourceProvider;
use Anny\Integrations\Console\Commands\JsonApi\MakeSchema;
use Anny\Integrations\Console\Commands\JsonApi\MakeValidators;
use Anny\Integrations\Console\Commands\ListenerMakeCommand;
use Anny\Integrations\Console\Commands\MailMakeCommand;
use Anny\Integrations\Console\Commands\MiddlewareMakeCommand;
use Anny\Integrations\Console\Commands\ModelMakeCommand;
use Anny\Integrations\Console\Commands\NotificationMakeCommand;
use Anny\Integrations\Console\Commands\ObserverMakeCommand;
use Anny\Integrations\Console\Commands\PolicyMakeCommand;
use Anny\Integrations\Console\Commands\ProviderMakeCommand;
use Anny\Integrations\Console\Commands\RequestMakeCommand;
use Anny\Integrations\Console\Commands\ResourceMakeCommand;
use Anny\Integrations\Console\Commands\RuleMakeCommand;
use Anny\Integrations\Contracts\EncryptSettingsService as EncryptSettingsServiceContract;
use Anny\Integrations\Contracts\IntegrationModel as IntegrationContract;
use Anny\Integrations\Contracts\IntegrationsRegistry as IntegrationsRegistryContract;
use Anny\Integrations\Jobs\RenewWebhookSubscriptions;
use Anny\Integrations\Models\Integration;
use Anny\Integrations\Models\IntegrationWebhookCall;
use Anny\Integrations\Models\IntegrationWebhookSubscription;
use Anny\Integrations\Observers\IntegrationModelObserver;
use Anny\Integrations\Services\EncryptSettingsService;
use Illuminate\Console\Scheduling\Schedule;
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
                __DIR__.'/../database/migrations/create_failed_integration_jobs_table.php' => $this->getMigrationFileName('create_failed_integration_jobs_table.php', $filesystem),
                __DIR__.'/../database/migrations/create_integration_webhook_subscriptions_table.php' => $this->getMigrationFileName('create_integration_webhook_subscriptions_table.php', $filesystem),
                __DIR__.'/../database/migrations/create_integration_webhook_calls_table.php' => $this->getMigrationFileName('create_integration_webhook_calls_table.php', $filesystem),
		    ], 'migrations');
	    }

	    // Load and publish views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'anny');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/anny'),
        ]);

	    // Register models
	    $integrationModel = config('integrations.integrationModel') ?: Integration::class;
        $integrationWebhookCallModel = config('integrations.integrationWebhookCallModel') ?: IntegrationWebhookCall::class;
        $integrationWebhookSubscriptionModel = config('integrations.integrationWebhookSubscriptionModel') ?: IntegrationWebhookSubscription::class;#

	    $this->app->bind(IntegrationContract::class, $integrationModel);

        // Set model and observe it
	    Integrations::useModel($integrationModel);
        Integrations::newModel()::observe(new IntegrationModelObserver());
        Integrations::useWebhookCallModel($integrationWebhookCallModel);
        Integrations::useWebhookSubscriptionModel($integrationWebhookSubscriptionModel);

        // Schedule jobs
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            if(Integrations::$shouldRunWebhookSubscriptionRenewal) {
                $threshold = Integrations::$webhookSubscriptionRenewalThreshold;
                $cron = "0 */${threshold} * * *";
                $schedule->job(new RenewWebhookSubscriptions())->cron($cron);
            }
        });

	    Relation::morphMap([
	    	'integrations' => $integrationModel,
            'integrations-webhook-calls' => $integrationWebhookCallModel,
            'integrations-webhook-subscriptions' => $integrationWebhookSubscriptionModel
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