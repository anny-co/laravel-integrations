<?php


namespace Bddy\Integrations\Traits;


use Bddy\Integrations\Contracts\IntegrationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

trait IsIntegrationModel
{
	/**
	 * @var string
	 */
	protected string $integrationKeyKey = 'key';

    /**
     * Initialize the IsIntegrationModel trait and set casts and hidden.
     */
    public function initializeIsIntegrationModel()
    {
        $this->casts = array_merge(
            [
                'active'                  => 'boolean',
                'settings'                => 'json',
                'authentication_required' => 'boolean',
            ],
            $this->casts,
        );

        $this->hidden = array_merge(
            [
                'error_details'
            ],
            $this->hidden,
        );

        $this->fillable = array_merge(
            ['name', 'key', 'version', 'model_type', 'model_id', 'uuid', 'settings'],
            $this->fillable,
        );
	}

    /**
     * Boot this trait and register events.
     */
    public static function bootIsIntegrationModel()
    {
        static::creating(function(Model $integration) {
            $integration->uuid = Str::uuid();
        });
	}

	/**
	 * Get key of a integration
	 *
	 * @return mixed
	 */
	public function getIntegrationKey(): string
	{
		return $this->getAttribute($this->integrationKeyKey);
	}

	/**
	 * Relation to model which has this integration.
	 *
	 * @return MorphTo
	 */
	public function model(): MorphTo
    {
		return $this->morphTo('model');
	}

    /**
     * Get manager for this integration instance.
     *
     * @return IntegrationManager
     */
    public function getIntegrationManager(): IntegrationManager
    {
        return integrations()->getIntegrationManager($this->getIntegrationKey())->for($this);
	}


    /**
     * Activate a specific integration model.
     *
     * @return $this
     */
    public function activateIntegration(): static
    {
		$this->getIntegrationManager()->activate($this);

		return $this;
	}

	/**
	 * Deactivate a specific integration model.
	 *
	 * @return $this
	 */
	public function deactivateIntegration(): static
	{
		$this->getIntegrationManager()->deactivate($this);

		return $this;
	}

	/**
	 * Initialize a specific integration model.
	 *
	 * @return mixed
	 */
	public function initializeIntegration(): static
	{
		$this->getIntegrationManager()->initialize($this);

		return $this;
	}

	/**
     * TODO: replace with model events
	 * Updating a specific integration model.
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	public function updatingIntegration(array $attributes): array
    {
		return $this->getIntegrationManager()
			->updating($this, $attributes);
	}
}