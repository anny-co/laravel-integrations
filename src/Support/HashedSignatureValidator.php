<?php

namespace Anny\Integrations\Support;

use Anny\Integrations\Contracts\SignatureValidator;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Exceptions\EmptySignatureException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class HashedSignatureValidator implements SignatureValidator
{
    public function __construct(protected string $signatureHeaderName)
    {
    }

    public function isValid(Request $request, WebhookSubscription|Model $webhookSubscription): bool
    {
        $signature = $request->header($this->signatureHeaderName);

        if (! $signature) {
            return false;
        }

        $signingSecret = $webhookSubscription->getSecret();

        if (empty($signingSecret)) {
            throw new EmptySignatureException();
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $signingSecret);

        return hash_equals($signature, $computedSignature);
    }
}