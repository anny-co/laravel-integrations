<?php

namespace Bddy\Integrations\Exceptions;

use Bddy\Integrations\Contracts\IntegrationModel;
use Throwable;

class IntegrationIsLockedException extends \Exception
{

    /**
     * @param IntegrationModel $integration
     * @param Throwable|null   $previous
     */
    public function __construct(IntegrationModel $integration, Throwable $previous = null)
    {
        parent::__construct('Integration is currently locked. Please try again later.', 3300, $previous);
    }
}