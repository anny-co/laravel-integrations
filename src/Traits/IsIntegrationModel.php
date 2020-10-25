<?php


namespace Bddy\Integrations\Traits;


trait IsIntegrationModel
{
	/**
	 * @var string
	 */
	protected $integrationKeyKey = 'key';

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

	protected function getIntegrationKey()
	{
		return $this->getAttribute($this->integrationKeyKey);
	}

	/**
	 * Relation to integratable model
	 * @return \Illuminate\Database\Eloquent\Relations\MorphTo
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
			->getIntegration($this->getIntegrationKey())
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
			->getIntegration($this->getIntegrationKey())
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
			->getIntegration($this->getIntegrationKey())
			->initialize($this);
	}
}