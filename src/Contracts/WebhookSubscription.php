<?php

namespace Anny\Integrations\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WebhookSubscription
{
    public function getIntegration(): IntegrationModel|Model;

    public function getSecret();
}