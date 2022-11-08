<?php

namespace Anny\Integrations\Jobs;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Contracts\WebhookCall;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Integrations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhook extends AbstractIntegrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public WebhookCall|Model $webhookCall)
    {
        $this->queue = Integrations::$defaultQueue;
    }

    public function getIntegration(): IntegrationModel|Model
    {
        return $this->webhookCall->getIntegration();
    }
}