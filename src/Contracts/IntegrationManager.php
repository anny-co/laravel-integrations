<?php


namespace Bddy\Integrations\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Integration interface for a manager of an specific integration.
 * For example a Zoom integration would implement
 *
 *
 * @package Bddy\Integrations\Contracts
 */
interface IntegrationManager
{

	/**
	 * Get instance of integration.
	 * @return static
	 */
	public static function get();

	/**
	 * Returns the identifier name for this integration.
	 *
	 * @returns string
	 */
	public static function getIntegrationKey(): string;

	/**
	 * Returns the default config for an integration.
	 *
	 * @return array
	 */
	public function getDefaultSettings(): array;

	/**
	 * Get specific setting of integration. It will retrieve a default setting when setting is not found and default is null.+
	 * If setting is not found and default is not null it will return default.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of values.
	 *
	 * @param array|string|null $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	public function setting($key, $default = null): mixed;


	/**
	 * Return definitions of integration.
	 *
	 * @return array
	 */
	public function getDefinitions(): array;

	/**
	 * Override default rules.
	 *
	 * @return array
	 */
	public function rules();

	/**
	 * Get rules for settings.
	 *
	 * @return array
	 */
	public function settingRules();

	/**
	 * Retrieve integration model from related model.
	 *
	 * @param HasIntegrations $model
	 *
	 * @return Model|Integration|null
	 */
	public function retrieveModelFrom(HasIntegrations $model);

	/**
	 * Set the model for which the next actions should be taken.
	 *
	 * @param Model|Integration $integration
	 *
	 * @return self
	 */
	public function for(Integration $integration);

	/**
	 * Activate a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function activate(?Integration $integration);

	/**
	 * Deactivate a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?Integration $integration);

	/**
	 * Initialize a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?Integration $integration);

	/**
	 * Updating a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 * @param array|null $attributes
	 *
	 * @return mixed
	 */
	public function updating(?Integration $integration, array $attributes);
}