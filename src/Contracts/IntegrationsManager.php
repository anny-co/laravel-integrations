<?php


namespace Bddy\Integrations\Contracts;


interface IntegrationsManager
{
	/**
	 * Let the manager know about a new integration.
	 * @param Integration $integration
	 *
	 * @return mixed
	 */
	public function registerIntegration(Integration $integration);


	/**
	 * Return all registered integrations.
	 * @return mixed
	 */
	public function getIntegrations();

	/**
	 * Get a integration by it's key.
	 *
	 * @param string $key
	 *
	 * @return Integration
	 */
	public function getIntegration(string $key): Integration;
}