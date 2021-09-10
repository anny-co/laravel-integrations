<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Contracts\HasErrors;
use Bddy\Integrations\Contracts\HasFailures;
use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Traits\HandlesErrorsAndFailures;
use Bddy\Integrations\Traits\HasManifest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class AbstractIntegrationManager implements IntegrationManager, HasErrors, HasFailures
{
    use HasManifest;
    use HandlesErrorsAndFailures;

	/**
	 * Key of integration.
	 */
	protected static string $integrationKey;

	/**
	 * Current integration model.
	 * @var null|Model|IntegrationModel
	 */
	protected $integration = null;

	/**
	 * Get instance from manager.
	 *
	 * @return IntegrationManager
	 */
	public static function get(): static
	{
		return integrations()->getIntegrationManager(
			static::getIntegrationKey()
		);
	}

	/**
	 * Set the model for which the next actions should be taken.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return static
	 */
	public function for(?IntegrationModel $integration = null): static
    {
		if($integration){
			$this->integration = $integration;
		}

		return $this;
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
	 * Get specific setting of integration. It will retrieve a default setting when setting is not found and default is null.
	 * If setting is not found and default is not null it will return default.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of values.
	 *
	 * @param array|string|null $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	public function setting(array|string|null $key, mixed $default = null): mixed
    {
		// Return all settings
		if(is_null($key)){
			return $this->integration->settings;
		}

		// Set values
		if(is_array($key)){
			// Set each key
			$settings = $this->integration->settings;
			foreach ($key as $keyString){
				Arr::set($settings, $keyString, $default);
			}
			$this->integration->settings = $settings;
			return $default;
		}

		// Return specific setting
		$value = Arr::get($this->integration->settings, $key, $default);
		if(!$value){
			// Return default from default settings
			return Arr::get($this->getDefaultSettings(), $key, $default);
		}

		return $value;
	}

	/**
	 * Retrieve integration model from related model which owns the integration.
	 *
	 * @param Model|HasIntegrations $model
	 *
	 * @return Model|IntegrationModel|null
	 */
	public function retrieveModelFrom(HasIntegrations $model): Model|IntegrationModel|null
	{
		return $model
			->integrations()
			->where('model_type', '=', $model->getMorphClass())
			->where('model_id', '=', $model->getKey())
			->where('key', '=', static::getIntegrationKey())
			->first();
	}

	/**
	 * Activate a specific integration model.
	 *
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
	 *
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
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?IntegrationModel $integration){
		$this->for($integration);

		return $this;
	}

	/**
	 * Updating a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 * @param array                       $attributes
	 *
	 * @return mixed
	 */
	public function updating(?IntegrationModel $integration, array $attributes) {
		$this->for($integration);

		return $attributes;
	}

    /**
     * Get connected manifest.
     *
     * @return IntegrationManifest
     */
    public function getManifest(): IntegrationManifest
    {
        /** @var IntegrationManifest $manifest */
        $manifest = new ($this->getManifestClass());

        return $manifest->setKey(
            self::getIntegrationKey()
        );
    }
}