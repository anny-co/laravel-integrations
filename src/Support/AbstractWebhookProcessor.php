<?php

namespace Anny\Integrations\Support;

use Anny\Integrations\Contracts\WebhookCall;
use Anny\Integrations\Contracts\WebhookProcessor;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Exceptions\EmptySignatureException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

abstract class AbstractWebhookProcessor implements WebhookProcessor
{

    protected string $signatureHeaderName = '';

    /**
     * @param Request                   $request
     * @param Model|WebhookSubscription $webhookSubscription
     *
     * @return bool
     * @throws EmptySignatureException
     */
    public function validateRequest(Request $request, Model|WebhookSubscription $webhookSubscription): bool
    {
        if($this->signatureHeaderName) {
            $validator = new HashedSignatureValidator($this->signatureHeaderName);

            return $validator->isValid($request, $webhookSubscription);
        }

        return false;
    }

    /**
     * @param Model|WebhookCall         $webhookCall
     * @param Model|WebhookSubscription $webhookSubscription
     *
     * @return void
     */
    public function process(Model|WebhookCall $webhookCall, Model|WebhookSubscription $webhookSubscription): void
    {
        $processor = $this->getProcessMap()->get($webhookCall->getType());

        // Do nothing
        if(is_null($processor)) {
            return;
        }

        // Check if it's a callable
        if(is_callable($processor)) {
            $processor($webhookCall, $webhookSubscription);

            return;
        }

        // Dispatch job
        if(class_exists($processor)) {
            dispatch(new $processor($webhookCall, $webhookSubscription));
        }
    }

    /**
     * Return job map.
     *
     * @return Collection
     */
    abstract protected function getProcessMap(): Collection;

    /**
     * @return Response
     */
    public function createResponse(): Response
    {
        return new Response([
            'message' => 'ok'
        ]);
    }

    abstract public function isSyncRequest(Request $request): bool;

    abstract public function processSyncRequest(Request $request): Response;

    abstract public function makeWebhooksFromRequest(Request $request, Model|WebhookSubscription $webhookSubscription): Collection;

    abstract public function shouldProcess(Model|WebhookCall $webhookCall, Model|WebhookSubscription $webhookSubscription): bool;
}