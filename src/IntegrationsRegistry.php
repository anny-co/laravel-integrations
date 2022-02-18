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
    private array $integrations = [
        'default' => [],
    ];

    public function __construct()
    {

    }

    /**
     * @param IntegrationManager $manager
     * @param string             $type
     *
     * @return IntegrationManager
     */
    public function registerIntegrationManager(IntegrationManager $manager, string $type = 'default')
    {
        if($type === '') {
            $type = 'default';
        }

        $this->integrations[$type][$manager->getIntegrationKey()] = $manager;

        return $manager;
    }

    /**
     * @return array|mixed
     */
    public function getIntegrationManagers(string $type = 'default')
    {
        return Arr::get($this->integrations, $type, []);
    }

    /**
     * @param string $key
     * @param string $type
     *
     * @return IntegrationManager|null
     */
    public function getIntegrationManager(string $key, string $type = 'default'): IntegrationManager|null
    {
        return Arr::get($this->integrations, $type . '.' . $key);
    }
}