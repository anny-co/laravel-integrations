<?php


namespace Bddy\Integrations;

use Bddy\Integrations\Contracts\Integration;
use Bddy\Integrations\Contracts\IntegrationsManager as IntegrationsManagerContract;
use Illuminate\Support\Arr;

class IntegrationsManager implements IntegrationsManagerContract
{
	/**
	 * @var array Integrations $integrations
	 */
	private array $integrations = [];

	public function __construct()
	{

	}

	/**
	 * @param Integration $integration
	 *
	 * @return Integration|mixed
	 */
	public function registerIntegration(Integration $integration)
	{
		$this->integrations[$integration->getIntegrationKey()] = $integration;

		return $integration;
	}

	/**
	 * @return array|mixed
	 */
	public function getIntegrations()
	{
		return $this->integrations;
	}

	/**
	 * @param string $key
	 *
	 * @return Integration
	 */
	public function getIntegration(string $key): Integration
	{
		return Arr::get($this->integrations, $key);
	}
}