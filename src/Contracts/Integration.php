<?php


namespace Bddy\Integrations\Contracts;


interface Integration
{

	/**
	 * Activate a specific integration model.
	 *
	 * @return mixed
	 */
	public function activateIntegration();

	/**
	 * Deactivate a specific integration model.
	 *
	 * @return mixed
	 */
	public function deactivateIntegration();

	/**
	 * Initialize a specific integration model.
	 *
	 * @return mixed
	 */
	public function initializeIntegration();
}