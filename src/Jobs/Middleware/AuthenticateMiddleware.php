<?php

namespace Bddy\Integrations\Jobs\Middleware;

use Bddy\Integrations\Exceptions\IntegrationIsLockedException;
use Bddy\Integrations\Exceptions\RefreshTokenFailedException;
use Bddy\Integrations\Jobs\AbstractIntegrationJob;
use Closure;

class AuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param AbstractIntegrationJob $job
     * @param \Closure               $next
     *
     * @return mixed
     */
    public function handle(AbstractIntegrationJob $job, Closure $next)
    {
        $integration = $job->getIntegration();
        $manager = $integration->getIntegrationManager();

        try {
            $manager->authenticate();
        } catch (IntegrationIsLockedException $e) {
            return $job->release(10);
        } catch (RefreshTokenFailedException $e) {
            // Job cannot be restarted, user has to manually log in
            // This should ignore max attempts
            return $job->job->fail($e);
        }

        return $next($job);
    }
}