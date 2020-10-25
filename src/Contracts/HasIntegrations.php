<?php


namespace Bddy\Integrations\Contracts;


use Bddy\Integrations\Contracts\Integration as IntegrationContract;
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
	 * @param IntegrationContract $integration
	 *
	 * @return bool
	 */
	public function hasIntegration(IntegrationContract $integration);

	/**
	 * @param IntegrationContract $integration
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function getIntegration(IntegrationContract $integration);
}