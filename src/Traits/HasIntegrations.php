<?php


namespace Bddy\Integrations\Traits;


use Bddy\Integrations\Contracts\Integration as IntegrationContract;
use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Models\Integration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;


trait HasIntegrations
{
//	use HasRelationships;

	/**
	 * Relation of many integrations
	 * @return MorphMany
	 */
	public function integrations()
	{
		return $this->morphMany(config('integrations.integrationModel', Integration::class), 'model');
	}

	/**
	 * Check if model already has an integration
	 *
	 * @param  $integrationManager
	 *
	 * @return bool
	 */
	public function hasIntegration(IntegrationManager $integrationManager)
	{
		return $this->integrations()
				->where('key', $integrationManager::getIntegrationKey())
				->count() > 0;
	}

	/**
	 * @param  $integrationManager
	 *
	 * @return Model|IntegrationContract
	 */
	public function getIntegration(IntegrationManager $integrationManager)
	{
		return $this->integrations()
			->where('key', $integrationManager::getIntegrationKey())
			->first();
	}
}