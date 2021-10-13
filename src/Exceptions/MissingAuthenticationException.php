<?php

namespace Anny\Integrations\Exceptions;

use Throwable;

class MissingAuthenticationException extends \Exception
{

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct('Required data for authentication is missing.', $code, $previous);
    }
}