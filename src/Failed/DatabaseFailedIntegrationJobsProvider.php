<?php


namespace Anny\Integrations\Failed;

use Anny\Integrations\Contracts\IntegrationModel;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Queue\Failed\FailedJobProviderInterface;
use Illuminate\Support\Facades\Date;

class DatabaseFailedIntegrationJobsProvider implements FailedJobProviderInterface
{

	/**
	 * The connection resolver implementation.
	 *
	 * @var ConnectionResolverInterface
	 */
	protected $resolver;

	/**
	 * The database connection name.
	 *
	 * @var string
	 */
	protected $database;

	/**
	 * The database table.
	 *
	 * @var string
	 */
	protected $table;


	/**
	 * @var IntegrationModel|Model $integration
	 */
	protected $integration;

	/**
	 * Create a new database failed job provider.
	 *
	 * @param ConnectionResolverInterface $resolver
	 * @param  string                     $database
	 * @param  string                     $table
	 *
	 * @return void
	 */
	public function __construct(ConnectionResolverInterface $resolver, $database, $table, $integration)
	{
		$this->table = $table;
		$this->resolver = $resolver;
		$this->database = $database;
		$this->integration = $integration;
	}

	/**
	 * Log a failed job into storage.
	 *
	 * @param  string  $connection
	 * @param  string  $queue
	 * @param  string  $payload
	 * @param  \Throwable  $exception
	 * @return int|null
	 */
	public function log($connection, $queue, $payload, $exception)
	{
		$payloadDecoded = json_decode($payload, true);

		if(is_null($queue)){
			$queue = '';
		}

		if(is_null($connection)){
			$connection = '';
		}

		$this->getTable()->insert([
			'uuid' => $uuid = $payloadDecoded['uuid'],
			'display_name' => $payloadDecoded['displayName'],
			'integration_uuid' => $this->integration->uuid,
			'connection' => $connection,
			'queue' => $queue,
			'payload' => $payload,
			'exception' => (string) $exception,
			'failed_at' => Date::now(),
		]);

		return $uuid;
	}

	/**
	 * Get a list of all of the failed jobs.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->getTable()
			->where('integration_uuid', $this->integration->uuid)
			->orderBy('id', 'desc')
			->get()
			->map(function ($record) {
				$record->id = $record->uuid;

				return $record;
			})->all();
	}

	/**
	 * Get a single failed job.
	 *
	 * @param  mixed  $id
	 * @return object|null
	 */
	public function find($id)
	{
		$record = $this->getTable()
			->where('integration_uuid', $this->integration->uuid)
			->where('uuid', $id)
			->first();

		if ($record) {
			$record->id = $record->uuid;
		}

		return $record;
	}

	/**
	 * Delete a single failed job from storage.
	 *
	 * @param  mixed  $id
	 * @return bool
	 */
	public function forget($id)
	{
		return $this->getTable()
				->where('integration_uuid', $this->integration->uuid)
				->where('uuid', $id)
				->delete() > 0;
	}

	/**
	 * Flush all the failed jobs from storage.
	 *
	 * @return void
	 */
	public function flush()
	{
		$this->getTable()
			->where('integration_uuid', $this->integration->uuid)
			->delete();
	}

	/**
	 * Get a new query builder instance for the table.
	 *
	 * @return Builder
	 */
	protected function getTable()
	{
		return $this->resolver->connection($this->database)->table($this->table);
	}
}