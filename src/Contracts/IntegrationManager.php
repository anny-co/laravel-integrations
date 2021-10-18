<?php


namespace Anny\Integrations\Contracts;

use Anny\Integrations\Support\IntegrationManifest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;

/**
 * Integration interface for a manager of an specific integration.
 * For example a Zoom integration would implement
 *
 *
 * @package Anny\Integrations\Contracts
 */
interface IntegrationManager
{

	/**
	 * Get instance of integration.
	 * @return static
	 */
	public static function get(): static|IntegrationManager;

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
	public function setting(array|string|null $key, mixed $default = null): mixed;


    /**
     * Returns manifest for this integration.
     *
     * @return IntegrationManifest
     */
	public function getManifest(): IntegrationManifest;

    /**
     * Returns http client for this integration.
     *
     * @return PendingRequest
     */
    public function httpClient(): PendingRequest;

    /**
     * Check if connection to integration is successful.
     *
     * @return bool
     */
    public function testConnection(): bool;

	/**
	 * Override default rules.
	 *
	 * @return array
	 */
	public function rules(): array;

	/**
	 * Get rules for settings.
	 *
	 * @return array
	 */
	public function settingRules(): array;

	/**
	 * Retrieve integration model from related model.
	 *
	 * @param HasIntegrations $model
	 *
	 * @return Model|IntegrationModel|null
	 */
	public function retrieveModelFrom(HasIntegrations $model): Model|IntegrationModel|null;

	/**
	 * Set the model for which the next actions should be taken.
	 *
	 * @param Model|IntegrationModel $integration
	 *
	 * @return self
	 */
	public function for(IntegrationModel $integration): static;

	/**
	 * Activate a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function activate(?IntegrationModel $integration);

	/**
	 * Deactivate a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?IntegrationModel $integration);

	/**
	 * Initialize a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?IntegrationModel $integration);

	/**
	 * Updating a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 * @param array|null                  $attributes
	 *
	 * @return mixed
	 */
	public function updating(?IntegrationModel $integration, array $attributes);
}