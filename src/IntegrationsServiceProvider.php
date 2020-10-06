<?php

namespace Bddy\Integrations;

use Bddy\Integrations\Contracts\IntegrationsManager as IntegrationsManagerContract;
use Illuminate\Foundation\Application;
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
     * @return void
     */
    public function boot()
    {
    	// Load migrations
	    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

	    // Publish migrations
	    $this->publishes([
		    __DIR__.'/../config/integrations.php' => config_path('integrations.php')
	    ], 'config');
    }
}
