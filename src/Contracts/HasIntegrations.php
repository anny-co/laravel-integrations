<?php


namespace Anny\Integrations\Contracts;


use Anny\Integrations\Contracts\IntegrationManager as IntegrationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface HasIntegrations
{

	/**
	 * Relation of many integrations
	 * @return MorphMany
	 */
	public function integrations(): MorphMany;

	/**
	 * Check if model already has an integration
	 *
	 * @param IntegrationManager $integrationManager
	 *
	 * @return bool
	 */
	public function hasIntegration(IntegrationManager $integrationManager): bool;

	/**
	 * Check if model already has an active integration
	 *
	 * @param IntegrationManager $integrationManager
	 *
	 * @return bool
	 */
	public function hasActiveIntegration(IntegrationManager $integrationManager): bool;

	/**
	 * @param IntegrationManager $integrationManager
	 *
	 * @return Model|IntegrationModel
	 */
	public function getIntegration(IntegrationManager $integrationManager): Model|IntegrationModel;
}