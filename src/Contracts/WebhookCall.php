<?php

namespace Anny\Integrations\Contracts;

use Illuminate\Database\Eloquent\Model;

interface WebhookCall
{
    public function getIntegration(): IntegrationModel|Model;

    public function getType(): string;
}