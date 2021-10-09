<?php

namespace Anny\Integrations\Auth;

use Anny\Integrations\Contracts\AuthenticationStrategy;
use Anny\Integrations\Contracts\IntegrationModel;

abstract class AccessTokenAuthenticationStrategy extends AbstractAuthenticationStrategy implements AuthenticationStrategy
{
    /**
     * Identifier key for this authentication strategy.
     *
     * @var string
     */
    protected string $key = 'access_token';

    /**
     * Authenticate the integration.
     *
     * @param IntegrationModel $integration
     *
     * @return mixed
     */
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