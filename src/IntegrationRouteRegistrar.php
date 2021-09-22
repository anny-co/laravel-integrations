<?php

namespace Bddy\Integrations;

use Bddy\Integrations\Http\Controllers\OAuth2Controller;
use Illuminate\Support\Facades\Route;

class IntegrationRouteRegistrar
{

    /**
     * Indicates if the routes have been registered.
     *
     * @var bool
     */
    protected bool $registered = false;

    /**
     * Sets the prefix for routes.
     *
     * @var string
     */
    protected string $prefix = 'integrations';

    /**
     * Sets the middlewares to use with routes.
     *
     * @var string|array|null
     */
    protected string|array|null $middleware = 'web';

    /**
     * @param string $prefix
     *
     * @return $this
     */
    public function prefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function register()
    {
        $this->registered = true;

        $defineRoutes = function() {
            Route::prefix($this->prefix)
                ->middleware($this->middleware)
                ->group(function(){
                    Route::get('/auth/{uuid}/oauth2/redirect', [OAuth2Controller::class, 'redirect']);
                    Route::get('/auth/{key}/oauth2/callback', [OAuth2Controller::class, 'callback']);
                });
        };

        app()->booted($defineRoutes);
    }

    /**
     * Handle the object's destruction and register the router route.
     *
     * @return void
     */
    public function __destruct()
    {
        if (! $this->registered) {
            $this->register();
        }
    }
}