<?php

namespace Anny\Integrations\Exceptions;

use Anny\Integrations\Contracts\IntegrationModel;
use Throwable;

class RefreshTokenFailedException extends \Exception
{
    /**
     * @param IntegrationModel $integration
     * @param string           $message
     * @param int              $code
     * @param Throwable|null   $previous
     */
    public function __construct(public IntegrationModel $integration, $message = "", $code = 3400, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}