<?php

namespace Anny\Integrations\Jobs;

use Anny\Integrations\Contracts\WebhookCall;
use Anny\Integrations\Contracts\WebhookSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ProcessWebhooks
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Collection $webhookCalls, public WebhookSubscription|Model $webhookSubscription)
    {

    }

    public function handle()
    {
        // Get processor
        $integration = $this->webhookSubscription->getIntegration();
        $manager = $integration->getIntegrationManager();
        $processor = $manager->getWebhookProcessor();

        $this->webhookCalls->each(function(WebhookCall|Model $webhookCall) use ($processor) {
            $processor->process($webhookCall, $this->webhookSubscription);
        });
    }
}