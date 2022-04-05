<?php

namespace Anny\Integrations\Services;

use Anny\Integrations\Contracts\WebhookCall;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Exceptions\MissingWebhookProcessor;
use Anny\Integrations\Jobs\ProcessWebhooks;
use Anny\Integrations\Traits\Makeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WebhooksService
{
    use Makeable;

    /**
     * @param Request                   $request
     * @param WebhookSubscription|Model $webhookSubscription
     *
     * @return Response
     * @throws MissingWebhookProcessor
     */
    public function process(Request $request, WebhookSubscription|Model $webhookSubscription): Response
    {
        $integration = $webhookSubscription->getIntegration();
        $manager = $integration->getIntegrationManager();
        $processor = $manager->getWebhookProcessor();

        if(!$processor){
            throw new MissingWebhookProcessor();
        }

        // Validate signature
        $processor->validateRequest($request, $webhookSubscription);

        // Check for sync callback
        if($processor->isSyncRequest($request, $webhookSubscription)) {
            return $processor->processSyncRequest($request, $webhookSubscription);
        }

        // Get all webhooks from request
        // There are integrations (i.e. Microsoft), which could sending
        // multiple webhooks at once.
        $webhooks = $processor->makeWebhooksFromRequest($request, $webhookSubscription)
            ->filter(function (WebhookCall|Model $webhookCall) use ($processor, $webhookSubscription) {
                return $processor->shouldProcess($webhookCall, $webhookSubscription);
            })->each(function (WebhookCall|Model $webhookCall){
                $webhookCall->save();
            });

        // Dispatch processing webhooks
        dispatch(new ProcessWebhooks($webhooks, $webhookSubscription));

        return $processor->createResponse();
    }
}