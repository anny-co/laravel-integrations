<?php

namespace Anny\Integrations\Jobs;

use Anny\Integrations\Contracts\HandlesErrorsAndFailures;
use Anny\Integrations\Contracts\IntegrationManager;
use Anny\Integrations\Contracts\IntegrationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

abstract class AbstractIntegrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Key for a failure.
     *
     * @var string
     */
    protected string $failKey;
    /**
     * Text why job failed.
     *
     * @var string
     */
    protected string $failText;

    /**
     * Create job.
     *
     * @param IntegrationModel|Model $integration
     */
    public function __construct(public IntegrationModel|Model $integration)
    {
    }

    /**
     * Handle Job failure.
     *
     * @param \Throwable $exception
     */
    public function failed(\Throwable $exception)
    {
        /** @var HandlesErrorsAndFailures|IntegrationManager $manager */
        $manager = $this->getIntegration()->getIntegrationManager();

        Log::info('Integration job failed: '.$exception->getMessage());

        // Add failure
        $manager->handleJobException(
            $exception,
            $this,
            $this->getFailKey(),
            $this->getFailText(),
            $this->getExplanation()
        );
    }

    /**
     * Returns the integration for this job.
     *
     * @return IntegrationModel|Model
     */
    public abstract function getIntegration(): IntegrationModel|Model;

    /**
     * Returns integration manager of integration.
     *
     * @return IntegrationManager
     */
    public function getIntegrationManager(): IntegrationManager
    {
        return $this->integration->getIntegrationManager();
    }

    /**
     * @return string
     */
    public function getFailKey(): string
    {
        return $this->failKey;
    }

    /**
     * @return string
     */
    public function getFailText(): string
    {
        return $this->failText;
    }

    /**
     * @return string
     */
    public function getExplanation(): string
    {
        return '';
    }
}