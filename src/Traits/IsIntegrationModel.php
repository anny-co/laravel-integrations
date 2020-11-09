<?php


namespace Bddy\Integrations\Traits;


use Illuminate\Database\Eloquent\Relations\MorphTo;

trait IsIntegrationModel
{
	/**
	 * @var string
	 */
	protected string $integrationKeyKey = 'key';

	/**
	 * Casts
	 *
	 * @var string[]
	 */
	protected $casts = [
		'active'                  => 'boolean',
		'settings'                => 'json',
		'authentication_required' => 'boolean',
	];

	/**
	 * Hide error details from user.
	 * @var string[]
	 */
	protected $hidden = [
		'error_details'
	];

	/**
	 * Get key of a integration
	 *
	 * @return mixed
	 */
	protected function getIntegrationKey()
	{
		return $this->getAttribute($this->integrationKeyKey);
	}

	/**
	 * Relation to integratable model.
	 *
	 * @return MorphTo
	 */
	public function model()
	{
		return $this->morphTo('model');
	}

	/**
	 * Activate a specific integration model.
	 *
	 * @return mixed
	 */
	public function activateIntegration()
	{
		integrations()
			->getIntegrationManager($this->getIntegrationKey())
			->activate($this);
	}

	/**
	 * Deactivate a specific integration model.
	 *
	 * @return mixed
	 */
	public function deactivateIntegration()
	{
		integrations()
			->getIntegrationManager($this->getIntegrationKey())
			->deactivate($this);
	}

	/**
	 * Initialize a specific integration model.
	 *
	 * @return mixed
	 */
	public function initializeIntegration()
	{
		integrations()
			->getIntegrationManager($this->getIntegrationKey())
			->initialize($this);
	}

	/**
	 * Updating a specific integration model.
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	public function updatingIntegration(array $attributes)
	{
		return integrations()
			->getIntegrationManager($this->getIntegrationKey())
			->updating($this, $attributes);
	}
}