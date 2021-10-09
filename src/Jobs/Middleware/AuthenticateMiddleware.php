<?php

namespace Anny\Integrations\Jobs\Middleware;

use Anny\Integrations\Exceptions\IntegrationIsLockedException;
use Anny\Integrations\Exceptions\RefreshTokenFailedException;
use Anny\Integrations\Jobs\AbstractIntegrationJob;
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