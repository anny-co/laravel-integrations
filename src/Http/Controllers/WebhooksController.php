<?php

namespace Anny\Integrations\Http\Controllers;

use Anny\Integrations\Exceptions\MissingWebhookProcessor;
use Anny\Integrations\Integrations;
use Anny\Integrations\Services\WebhooksService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class WebhooksController extends Controller
{
    /**
     * Invoke webhook processing.
     *
     * @param Request         $request
     * @param string          $subscriptionUuid
     * @param WebhooksService $service
     *
     * @return Response
     * @throws MissingWebhookProcessor
     */
    public function __invoke(Request $request, string $subscriptionUuid, WebhooksService $service): Response
    {
        $webhookSubscription = Integrations::newWebhookSubscriptionModel()
            ->newQuery()
            ->where('uuid', $subscriptionUuid)
            ->first();

        if(is_null($webhookSubscription)){
            throw new ModelNotFoundException();
        }

        // Find parser class
        return $service->process($request, $webhookSubscription);
    }


}