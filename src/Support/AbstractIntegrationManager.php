<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\Integration;
use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Failed\DatabaseFailedIntegrationJobsProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AbstractIntegrationManager implements IntegrationManager
{

	/**
	 * Flag if changed settings should directly be saved.
	 * @var bool
	 */
	protected bool $saveChanges = true;

	/**
	 * Key of integration.
	 */
	protected static string $integrationKey;

	/**
	 * Current integration model.
	 * @var null|Model|Integration
	 */
	protected $integration = null;

	/**
	 * Get instance from manager.
	 *
	 * @return IntegrationManager
	 */
	public static function get()
	{
		return integrations()->getIntegrationManager(
			static::getIntegrationKey()
		);
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
	 * Retrieve integration model from related model.
	 *
	 * @param Model|HasIntegrations $model
	 *
	 * @return Model|Integration|null
	 */
	public function retrieveModelFrom(HasIntegrations $model)
	{
		return $model
			->integrations()
			->where('model_type', '=', get_class($model))
			->where('model_id', '=', $model->getKey())
			->where('key', '=', static::getIntegrationKey())
			->first();
	}

	/**
	 * Set the model for which the next actions should be taken.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function for(?Integration $integration = null){
		if($integration){
			$this->integration = $integration;
		}

		return $this;
	}

	/**
	 * Activate a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function activate(?Integration $integration){
		$this->for($integration);
		$integration->active = true;
		return $integration->save();
	}


	/**
	 * Deactivate a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?Integration $integration){
		$this->for($integration);
		$integration->active = false;
		return $integration->save();
	}

	/**
	 * Initialize a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?Integration $integration){
		$this->for($integration);

		return $this;
	}

	/**
	 * Updating a specific integration model.
	 *
	 * @param Model|Integration|null $integration
	 * @param array                  $attributes
	 *
	 * @return mixed
	 */
	public function updating(?Integration $integration, array $attributes) {
		$this->for($integration);

		return $attributes;
	}

	/**
	 * @param string        $errorMessage
	 * @param \Throwable    $exception
	 * @param boolean|false $force
	 */
	public function saveError(string $errorMessage, \Throwable $exception, bool $force = false)
	{
		// Check if there is already an error
		if(!$force && $this->hasError()){
			return;
		}

		$this->integration->error = $errorMessage;
		$this->integration->error_details = [
			'class' => get_class($exception),
			'line' => $exception->getLine(),
			'message' => $exception->getMessage(),
			'code' => $exception->getCode(),
			'file' =>  $exception->getFile(),
			'trace' => $exception->getTraceAsString(),
		];

		if ($this->saveChanges) {
			$this->integration->save();
		}
	}


	/**
	 * Check if integration has an error.
	 *
	 * @return bool
	 */
	public function hasError()
	{
		return isset($this->error);
	}

	/**
	 * Remove error from integration
	 */
	public function removeError()
	{
		$this->integration->error = null;
		$this->integration->error_details = null;

		if ($this->saveChanges) {
			$this->integration->save();
		}
	}

	/**
	 * @param            $job
	 * @param \Throwable $exception
	 * @param string     $key
	 * @param string     $displayName
	 * @param string     $explanation
	 *
	 * @see \Illuminate\Queue\Failed\DatabaseUuidFailedJobProvider
	 */
	public function saveFailure($job, \Throwable $exception, string $key, string $displayName, string $explanation = '')
	{
		// @see Illuminate\Queue\Queue@createObjectPayload
		// Create payload
		$payload = $this->createPayload($job, $key, $displayName, $explanation);

		$this->createFailedIntegrationProvider()->log(
			$job->connection,
			$job->queue,
			json_encode($payload),
			$exception
		);
	}

	/**
	 * @param        $job
	 * @param string $key
	 * @param string $displayName
	 * @param string $explanation
	 *
	 * @return array
	 */
	public function createPayload($job, string $key, string $displayName, string $explanation = '')
	{
		$displayFailureText = "[$key] $displayName";
		if($explanation !== ''){
			$displayFailureText .= ": $explanation";
		}

		return [
			'uuid' => (string) Str::uuid(),
			'displayName' => $displayFailureText,
			'data' => [
				'commandName' => get_class($job),
				'command' => serialize(clone $job),
			],
		];
	}

	/**
	 * Create a failed job handler for current integration.
	 *
	 * @return DatabaseFailedIntegrationJobsProvider
	 */
	public function createFailedIntegrationProvider()
	{
		return new DatabaseFailedIntegrationJobsProvider(
			app('db'),
			env('DB_CONNECTION', 'mysql'),
			'failed_integration_jobs',
			$this->integration
		);
	}
}