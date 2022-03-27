<?php

namespace Anny\Integrations\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

interface WebhookProcessor
{
    public function validateRequest(Request $request, WebhookSubscription|Model $webhookSubscription): bool;

    public function isSyncRequest(Request $request): bool;

    public function processSyncRequest(Request $request): Response;

    public function makeWebhooksFromRequest(Request $request, WebhookSubscription|Model $webhookSubscription): Collection;

    public function shouldProcess(WebhookCall|Model $webhookCall, WebhookSubscription|Model $webhookSubscription): bool;

    public function process(WebhookCall|Model $webhookCall, WebhookSubscription|Model $webhookSubscription): void;

    public function createResponse(): Response;
}