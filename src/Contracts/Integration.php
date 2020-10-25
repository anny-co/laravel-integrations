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
interface Integration
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
	 * @param HasIntegrations $model
	 *
	 * @return mixed
	 */
	public function retrieveModelFrom(HasIntegrations $model);

	/**
	 * Set the model for which the next actions should be taken.
	 * @param Model|IntegrationModel $integration
	 *
	 * @return mixed
	 */
	public function for(IntegrationModel $integration);

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
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?IntegrationModel $integration);

	/**
	 * Initialize a specific integration model.
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?IntegrationModel $integration);
}