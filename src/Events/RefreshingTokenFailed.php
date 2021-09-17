<?php

namespace Bddy\Integrations\Events;

use Bddy\Integrations\Contracts\IntegrationModel;
use Illuminate\Http\Client\Response;

class RefreshingTokenFailed
{
    public function __construct(public IntegrationModel $integration, public Response $response)
    {
    }
}