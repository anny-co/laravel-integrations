<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\Integration;
use Bddy\Integrations\Contracts\IntegrationModel;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractIntegration implements Integration
{

	/**
	 * Key of integration.
	 */
	protected static $integrationKey = '';

	/**
	 * Current integration model.
	 * @var null|Model|IntegrationModel
	 */
	protected $integrationModel = null;

	/**
	 * Get instance from manager.
	 *
	 * @return Integration
	 */
	public static function get()
	{
		return integrations()->getIntegration(
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
	 * @return Model|IntegrationModel|null
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
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function for(?IntegrationModel $integration){
		if($integration){
			$this->integrationModel = $integration;
		}

		return $this;
	}

	/**
	 * Activate a specific integration model.
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function activate(?IntegrationModel $integration){
		$this->for($integration);
		$integration->active = true;
		return $integration->save();
	}


	/**
	 * Deactivate a specific integration model.
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?IntegrationModel $integration){
		$this->for($integration);
		$integration->active = false;
		return $integration->save();
	}

	/**
	 * Initialize a specific integration model.
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?IntegrationModel $integration){
		$this->for($integration);
	}

}