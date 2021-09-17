<?php


namespace Bddy\Integrations;

use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Contracts\IntegrationsRegistry as IntegrationsRegistryContract;
use Illuminate\Support\Arr;

class IntegrationsRegistry implements IntegrationsRegistryContract
{
    /**
     * @var array $integrations
     */
    private array $integrations = [];

    public function __construct()
    {

    }

    /**
     * @param IntegrationManager $integration
     *
     * @return IntegrationManager|mixed
     */
    public function registerIntegrationManager(IntegrationManager $integration)
    {
        $this->integrations[$integration->getIntegrationKey()] = $integration;

        return $integration;
    }

    /**
     * @return array|mixed
     */
    public function getIntegrationManagers()
    {
        return $this->integrations;
    }

    /**
     * @param string $key
     *
     * @return IntegrationManager
     */
    public function getIntegrationManager(string $key): IntegrationManager
    {
        return Arr::get($this->integrations, $key);
    }

    /**
     * Register routes for integrations.
     *
     * @return IntegrationRouteRegistrar
     */
    public static function routes(string $prefix = 'api')
    {
        return (new IntegrationRouteRegistrar())->prefix($prefix);
    }
}