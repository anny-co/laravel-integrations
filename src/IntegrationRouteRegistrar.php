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
    protected string $prefix = 'api';

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

        Route::prefix($this->prefix)
            ->group(function(){
                Route::get('integrations/{uuid}/oauth2/redirect', [OAuth2Controller::class, 'redirect']);
                Route::get('integrations/{key}/oauth2/callback', [OAuth2Controller::class, 'callback']);
            });
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