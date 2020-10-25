<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\Integration;
use Bddy\Integrations\Contracts\IntegrationModel;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractIntegration implements Integration
{

	/**
	 *
	 */
	protected const KEY = '';

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
		return integrations()->getIntegration(static::KEY);
	}

	/**
	 * Return integration key.
	 *
	 * @return string
	 */
	public function getIntegrationKey(): string
	{
		return self::KEY;
	}

	/**
	 * Retrieve integration model from related model.
	 *
	 * @param Model|HasIntegrations $model
	 *
	 * @return Model|IntegrationModel
	 */
	public function retrieveModelFrom(HasIntegrations $model): IntegrationModel
	{
		return $model
			->integrations()
			->where('model_type', '=', get_class())
			->where('model_id', '=', $model->getKey())
			->where('key', '=', self::KEY)
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
		$integration->active = true;
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