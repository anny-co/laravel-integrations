<?php

namespace Anny\Integrations\Events;

use Anny\Integrations\Contracts\IntegrationModel;

class OAuth2CallbackFinished
{

    public function __construct(public IntegrationModel $integration, array $accessTokenResponse = [])
    {
    }
}