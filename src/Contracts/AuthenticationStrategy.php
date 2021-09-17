<?php

namespace Bddy\Integrations\Contracts;

use Illuminate\Http\Client\PendingRequest;

interface AuthenticationStrategy
{

    /**
     * Return identifier key for authentication strategy.
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * Return an authenticated http client for this authentication strategy.
     *
     * @param IntegrationModel $integration
     *
     * @return PendingRequest
     */
    public function getHttpClient(IntegrationModel $integration): PendingRequest;

    /**
     * Return an authenticated http client for this authentication strategy.
     *
     * @param IntegrationModel $integration
     *
     * @return PendingRequest
     */
    public function getUnauthenticatedClient(IntegrationModel $integration): PendingRequest;

    /**
     * Check if the strategy has required data.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function hasRequiredData(IntegrationModel $integration): bool;

    /**
     * Check if the authentication strategy is authenticated.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function isAuthenticated(IntegrationModel $integration): bool;

    /**
     * Authenticate the integration.
     *
     * @param IntegrationModel $integration
     *
     * @return mixed
     */
    public function authenticate(IntegrationModel $integration): bool;

}