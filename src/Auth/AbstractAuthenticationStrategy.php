<?php

namespace Bddy\Integrations\Auth;

use Bddy\Integrations\Contracts\AuthenticationStrategy;

abstract class AbstractAuthenticationStrategy implements AuthenticationStrategy
{

    /**
     * Get key for this authentication method.
     *
     * @return string
     */
    public abstract function getKey(): string;
}