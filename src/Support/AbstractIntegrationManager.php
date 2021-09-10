<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Contracts\HasErrors;
use Bddy\Integrations\Contracts\HasFailures;
use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Failed\DatabaseFailedIntegrationJobsProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class AbstractIntegrationManager implements IntegrationManager, HasErrors, HasFailures
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
	 * @var null|Model|IntegrationModel
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
	 * Set the model for which the next actions should be taken.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return static
	 */
	public function for(?IntegrationModel $integration = null): static
    {
		if($integration){
			$this->integration = $integration;
		}

		return $this;
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
	 * Get specific setting of integration. It will retrieve a default setting when setting is not found and default is null.
	 * If setting is not found and default is not null it will return default.
	 *
	 * If an array is passed as the key, we will assume you want to set an array of values.
	 *
	 * @param array|string|null $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	public function setting($key, $default = null)
    {
		// Return all settings
		if(is_null($key)){
			return $this->integration->settings;
		}

		// Set values
		if(is_array($key)){
			// Set each key
			$settings = $this->integration->settings;
			foreach ($key as $keyString){
				Arr::set($settings, $keyString, $default);
			}
			$this->integration->settings = $settings;
			return $default;
		}

		// Return specific setting
		$value = Arr::get($this->integration->settings, $key, $default);
		if(!$value){
			// Return default from default settings
			return Arr::get($this->getDefaultSettings(), $key, $default);
		}

		return $value;
	}

	/**
	 * Retrieve integration model from related model which owns the integration.
	 *
	 * @param Model|HasIntegrations $model
	 *
	 * @return Model|IntegrationModel|null
	 */
	public function retrieveModelFrom(Model|HasIntegrations $model)
	{
		return $model
			->integrations()
			->where('model_type', '=', $model->getMorphClass())
			->where('model_id', '=', $model->getKey())
			->where('key', '=', static::getIntegrationKey())
			->first();
	}

	/**
	 * Activate a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function activate(?IntegrationModel $integration){
		$this->for($integration);
		$integration->active = true;
		return $integration->save();
	}


	/**
	 * Deactivate a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function deactivate(?IntegrationModel $integration){
		$this->for($integration);
		$integration->active = false;
		return $integration->save();
	}

	/**
	 * Initialize a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 *
	 * @return mixed
	 */
	public function initialize(?IntegrationModel $integration){
		$this->for($integration);

		return $this;
	}

	/**
	 * Updating a specific integration model.
	 *
	 * @param Model|IntegrationModel|null $integration
	 * @param array                       $attributes
	 *
	 * @return mixed
	 */
	public function updating(?IntegrationModel $integration, array $attributes) {
		$this->for($integration);

		return $attributes;
	}


    /*
     * The next functions handle error management.
     */

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
	public function hasError(): bool
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
	 * List all failures
	 *
	 * @return array
	 */
	public function listFailures(): array
	{
		return collect($this->createFailedIntegrationProvider()->all())
			->map(function ($record){
				return (object) [
					'uuid' => $record->uuid,
					'display_name' => $record->display_name,
					'integration_uuid' => $record->integration_uuid,
					'failed_at' => $record->failed_at
				];
			})
			->all();
	}

	/**
	 * Find a failure.
	 *
	 * @param string $uuid
	 *
	 * @return object|null
	 */
	public function findFailure(string $uuid): ?object
	{
		return $this->createFailedIntegrationProvider()->find($uuid);
	}

	/**
	 * Send a failure back on the queue.
	 *
	 * @param string $uuid
	 */
	public function retryFailure(string $uuid)
	{
		$record = $this->createFailedIntegrationProvider()->find($uuid);

		if(!$record){
			return;
		}

		$payload = json_decode($record->payload, true);
		$job = unserialize($payload['data']['command']);
		dispatch($job)->onConnection($record->connection)->onQueue($record->queue);

		//
		$this->forgetFailure($uuid);
	}

	/**
	 * Forget a specific failure.
	 *
	 * @param string $uuid
	 *
	 * @return bool
	 */
	public function forgetFailure(string $uuid): bool
	{
		return $this->createFailedIntegrationProvider()->forget($uuid);
	}

	/**
	 * Delete all failures.
	 *
	 * @return void
	 */
	public function flushFailures()
	{
		$this->createFailedIntegrationProvider()->flush();
	}

	/**
	 * @param        $job
	 * @param string $key
	 * @param string $displayName
	 * @param string $explanation
	 *
	 * @return array
	 */
	public function createPayload($job, string $key, string $displayName, string $explanation = ''): array
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
	public function createFailedIntegrationProvider(): DatabaseFailedIntegrationJobsProvider
    {
		return new DatabaseFailedIntegrationJobsProvider(
			app('db'),
			env('DB_CONNECTION', 'mysql'),
			'failed_integration_jobs',
			$this->integration
		);
	}
}