<?php

namespace Anny\Integrations\Events;

use Anny\Integrations\Contracts\IntegrationModel;
use Illuminate\Http\Client\Response;

class RefreshingTokenFailed
{
    public function __construct(public IntegrationModel $integration, public Response $response)
    {
    }
}