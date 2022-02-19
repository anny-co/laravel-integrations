<?php


namespace Anny\Integrations\Contracts;


interface IntegrationsRegistry
{
	/**
	 * Let the manager know about a new integration.
	 *
	 * @param IntegrationManager $integration
	 *
	 * @return mixed
	 */
	public function registerIntegrationManager(IntegrationManager $integration);


	/**
	 * Return all registered integration managers.
	 * @return mixed
	 */
	public function getIntegrationManagers();

    /**
     * Get a integration by it's key.
     *
     * @param string $key
     *
     * @return IntegrationManager|null
     */
	public function getIntegrationManager(string $key): IntegrationManager|null;
}