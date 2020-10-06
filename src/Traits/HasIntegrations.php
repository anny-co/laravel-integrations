<?php


namespace Bddy\Integrations\Traits;


use Bddy\Integrations\Contracts\Integration as IntegrationContract;
use Bddy\Integrations\Models\Integration;


trait HasIntegrations
{
//	use HasRelationships;

	/**
	 * Relation of many integrations
	 */
	public function integrations()
	{
		return $this->morphMany(Integration::class, 'model');
	}

	/**
	 * Check if model already has an integration
	 *
	 * @param IntegrationContract $integration
	 *
	 * @return bool
	 */
	public function hasIntegration(IntegrationContract $integration)
	{
		return $this->integrations()
				->where('key', $integration->getKey())
				->count() > 0;
	}

	/**
	 * @param IntegrationContract $integration
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function getIntegration(IntegrationContract $integration)
	{
		return $this->integrations()
			->where('key', $integration->getKey())
			->first();
	}
}