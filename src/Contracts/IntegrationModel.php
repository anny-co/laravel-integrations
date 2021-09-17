<?php


namespace Bddy\Integrations\Contracts;


interface IntegrationModel
{

    /**
     * Get corresponding integration manager.
     *
     * @return IntegrationManager
     */
    public function getIntegrationManager(): IntegrationManager;

    /**
     * Return array of secrets.
     *
     * @return array
     */
    public function getSecrets(): array;

    /**
     * Set array of secrets
     *
     * @param array $secrets
     *
     * @return static
     */
    public function setSecrets(array $secrets): static;

	/**
	 * Activate a specific integration model.
	 *
	 * @return mixed
	 */
	public function activateIntegration(): static;

	/**
	 * Deactivate a specific integration model.
	 *
	 * @return mixed
	 */
	public function deactivateIntegration(): static;

	/**
	 * Initialize a specific integration model.
	 *
	 * @return mixed
	 */
	public function initializeIntegration(): static;
}