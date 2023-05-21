<?php

namespace Anny\Integrations\Traits;

use Anny\Integrations\Failed\DatabaseFailedIntegrationJobsProvider;
use Illuminate\Support\Str;

trait HandlesErrorsAndFailures
{

    /**
     * Flag if changed settings should directly be saved.
     * @var bool
     */
    protected bool $saveChanges = true;

    /**
     * @param string        $errorMessage
     * @param \Throwable    $exception
     * @param boolean|false $force
     */
    public function saveError(string $errorMessage, \Throwable $exception, bool $force = false)
    {
        // Check if there is already an error
        if (!$force && $this->hasError())
        {
            return;
        }

        $this->integration->error         = $errorMessage;
        $this->integration->error_details = [
            'class'   => get_class($exception),
            'line'    => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code'    => $exception->getCode(),
            'file'    => $exception->getFile(),
            'trace'   => $exception->getTraceAsString(),
        ];

        if ($this->saveChanges)
        {
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
        return isset($this->integration->error);
    }

    /**
     * Remove error from integration
     */
    public function removeError(): void
    {
        $this->integration->error         = null;
        $this->integration->error_details = null;

        if ($this->saveChanges)
        {
            $this->integration->save();
        }
    }

    /**
     * @param \Throwable $e
     * @param            $job
     * @param string     $contextKey
     * @param string     $displayName
     */
    public function handleJobException(\Throwable $e, $job, string $contextKey = '', string $displayName = '', string $explanation = ''): void
    {
        $contextKey  = empty($contextKey) ? 'Unknown' : $contextKey;
        $displayName = empty($displayName) ? 'An unknown error occurred.' : $displayName;
        $explanation = empty($explanation) ? 'Please try to login again.' : $explanation;

        // Unknown error
        if ($job)
        {
            $this->saveFailure(
                $job,
                $e,
                $contextKey,
                $displayName,
                $explanation
            );
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
            ->map(function ($record) {
                return (object) [
                    'uuid'             => $record->uuid,
                    'display_name'     => $record->display_name,
                    'integration_uuid' => $record->integration_uuid,
                    'failed_at'        => $record->failed_at
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

        if (!$record)
        {
            return;
        }

        $payload = json_decode($record->payload, true);
        $job     = unserialize($payload['data']['command']);
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
        $displayFailureText = "[${key}] ${displayName}";
        if ($explanation !== '')
        {
            $displayFailureText .= ": $explanation";
        }

        return [
            'uuid'        => (string) Str::uuid(),
            'displayName' => $displayFailureText,
            'data'        => [
                'commandName' => get_class($job),
                'command'     => serialize(clone $job),
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