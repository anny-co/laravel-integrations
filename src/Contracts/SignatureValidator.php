<?php

namespace Anny\Integrations\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface SignatureValidator
{
    public function isValid(Request $request, WebhookSubscription|Model $webhookSubscription): bool;
}