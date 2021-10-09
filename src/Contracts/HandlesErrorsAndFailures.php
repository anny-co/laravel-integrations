<?php

namespace Anny\Integrations\Contracts;

interface HandlesErrorsAndFailures
{
    /**
     * @param string     $errorMessage
     * @param \Throwable $exception
     * @param bool       $force
     *
     * @return mixed
     */
    public function saveError(string $errorMessage, \Throwable $exception, bool $force = false);

    /**
     * Check if this has an error.
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Prune error from this context.
     */
    public function removeError(): void;

    /**
     * @param \Throwable $e
     * @param            $job
     * @param string     $contextKey
     * @param string     $displayName
     */
    public function handleJobException(\Throwable $e, $job, string $contextKey = '', string $displayName = '', string $explanation = ''): void;

    /**
     * Save a failure.
     *
     * @param            $job
     * @param \Throwable $exception
     * @param string     $key
     * @param string     $displayName
     * @param string     $explanation
     *
     * @return mixed
     */
    public function saveFailure($job, \Throwable $exception, string $key, string $displayName, string $explanation = '');

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