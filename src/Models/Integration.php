<?php

namespace Bddy\Integrations\Models;

use Bddy\Integrations\Contracts\Integration as IntegrationContract;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model implements IntegrationContract
{
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
}
