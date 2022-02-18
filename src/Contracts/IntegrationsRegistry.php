<?php


namespace Anny\Integrations\Contracts;


interface IntegrationsRegistry
{
	/**
	 * Let the manager know about a new integration.
	 *
	 * @param IntegrationManager $manager
	 *
	 * @return mixed
	 */
	public function registerIntegrationManager(IntegrationManager $manager);


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