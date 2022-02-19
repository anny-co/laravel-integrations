<?php


namespace Anny\Integrations;

use Anny\Integrations\Contracts\IntegrationManager;
use Anny\Integrations\Contracts\IntegrationsRegistry as IntegrationsRegistryContract;
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
     * @return IntegrationManager|null
     */
    public function getIntegrationManager(string $key): IntegrationManager|null
    {
        return Arr::get($this->integrations, $key, null);
    }
}