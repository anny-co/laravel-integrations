<?php


namespace Bddy\Integrations\Contracts;


use Bddy\Integrations\Contracts\IntegrationManager as IntegrationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasIntegrations
{

	/**
	 * Relation of many integrations
	 * @return MorphMany
	 */
	public function integrations();

	/**
	 * Check if model already has an integration
	 *
	 * @param IntegrationManager $integrationManager
	 *
	 * @return bool
	 */
	public function hasIntegration(IntegrationManager $integrationManager);

	/**
	 * @param IntegrationManager $integrationManager
	 *
	 * @return Model|Integration
	 */
	public function getIntegration(IntegrationManager $integrationManager);
}