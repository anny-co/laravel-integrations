<?php

namespace Anny\Integrations\Jobs;

use Anny\Integrations\Contracts\IntegrationManager;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Integrations;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenewWebhookSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $daysBetweenRuns = 2)
    {
        $this->queue = Integrations::$defaultQueue;
    }

    public function handle()
    {
        // Get all subscriptions
        Integrations::newWebhookSubscriptionModel()::query()
            ->where('active', true)
            ->where('expired_at', '<', now()->addHours(Integrations::$webhookSubscriptionRenewalThreshold))
            ->each(function(WebhookSubscription|Model $webhookSubscription){
                /** @var IntegrationManager $manager */
                $manager = $webhookSubscription->getIntegration()->getIntegrationManager();
                // Get webhook processor
                if($processor = $manager->getWebhookProcessor()) {
                    $processor->renewWebhookSubscription($webhookSubscription);
                }
            });
    }
}