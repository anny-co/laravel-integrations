<?php


namespace Anny\Integrations\Traits;


use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Contracts\IntegrationManager;
use Anny\Integrations\Models\Integration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasIntegrations
{

	/**
	 * Relation of many integrations
	 * @return MorphMany
	 */
	public function integrations(): MorphMany
    {
		return $this->morphMany(config('integrations.integrationModel', Integration::class), 'model');
	}

    /**
     * Check if model already has an integration
     *
     * @param IntegrationManager $integrationManager
     *
     * @return bool
     */
	public function hasIntegration(IntegrationManager $integrationManager): bool
    {
		return $this->integrations()
				->where('key', $integrationManager::getIntegrationKey())
				->count() > 0;
	}

    /**
     * Check if model already has an active integration
     *
     * @param IntegrationManager $integrationManager
     *
     * @return bool
     */
	public function hasActiveIntegration(IntegrationManager $integrationManager): bool
    {
		return $this->integrations()
				->where('key', $integrationManager::getIntegrationKey())
				->where('active', '=', true)
				->count() > 0;
	}

    /**
     * @param IntegrationManager $integrationManager
     *
     * @return Model|IntegrationModel
     */
	public function getIntegration(IntegrationManager $integrationManager): Model|IntegrationModel
    {
		return $this->integrations()
			->where('key', $integrationManager::getIntegrationKey())
			->first();
	}
}