<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Contracts\Integration;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractIntegrationManager implements IntegrationManager
{

	/**
	 * Key of integration.
	 */
	protected static string $integrationKey;

	/**
	 * Current integration model.
	 * @var null|Model|Integration
	 */
	protected $integration = null;

	/**
	 * Get instance from manager.
	 *
	 * @return IntegrationManager
	 */
	public static function get()
	{
		return integrations()->getIntegrationManager(
			static::getIntegrationKey()
		);
	}

	/**
	 * Return integration key.
	 *
	 * @return string
	 */
	public static function getIntegrationKey(): string
	{
		return static::$integrationKey;
	}

	/**
	 * Retrieve integration model from related model.
	 *
	 * @param Model|HasIntegrations $model
	 *
	 * @return Model|Integration|null
	 */
	public function retrieveModelFrom(HasIntegrations $model)
	{
		return $model
			->integrations()
			->where('model_type', '=', get_class($model))
			->where('model_id', '=', $model->getKey())
			->where('key', '=', static::getIntegrationKey())
			->first();
	}

	/**
	 * Set the model for which the next actions should be taken.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function for(?Integration $integration = null){
		if($integration){
			$this->integration = $integration;
		}

		return $this;
	}

	/**
	 * Activate a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function activate(?Integration $integration){
		$this->for($integration);
		$integration->active = true;
		return $integration->save();
	}


	/**
	 * Deactivate a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?Integration $integration){
		$this->for($integration);
		$integration->active = false;
		return $integration->save();
	}

	/**
	 * Initialize a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?Integration $integration){
		$this->for($integration);

		return $this;
	}

	/**
	 * Updating a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 * @param array                  $attributes
	 *
	 * @return mixed
	 */
	public function updating(?Integration $integration, array $attributes) {
		$this->for($integration);

		return $attributes;
	}

}