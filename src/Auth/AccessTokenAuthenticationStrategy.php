<?php

namespace Bddy\Integrations\Auth;

use Bddy\Integrations\Contracts\AuthenticationStrategy;
use Bddy\Integrations\Contracts\IntegrationModel;

abstract class AccessTokenAuthenticationStrategy extends AbstractAuthenticationStrategy implements AuthenticationStrategy
{
    /**
     * Identifier key for this authentication strategy.
     *
     * @var string
     */
    protected string $key = 'access_token';


    public function authenticate(IntegrationModel $integration): bool
    {
        return $this->hasRequiredData($integration);
    }

    /**
     * Check if the authentication strategy is authenticated.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function isAuthenticated(IntegrationModel $integration): bool
    {
        return $this->hasRequiredData($integration);
    }
}