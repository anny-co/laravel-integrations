<?php


namespace Anny\Integrations\Contracts;


interface HasFailures
{
	/**
	 * List all failures
	 *
	 * @return array
	 */
	public function listFailures(): array;

	/**
	 * Find a failure.
	 *
	 * @param string $uuid
	 *
	 * @return object|null
	 */
	public function findFailure(string $uuid): ?object;

	/**
	 * Send a failure back on the queue.
	 *
	 * @param string $uuid
	 */
	public function retryFailure(string $uuid);

	/**
	 * Forget a specific failure.
	 *
	 * @param string $uuid
	 *
	 * @return bool
	 */
	public function forgetFailure(string $uuid): bool;

	/**
	 * Delete all failures.
	 *
	 * @return void
	 */
	public function flushFailures();
}