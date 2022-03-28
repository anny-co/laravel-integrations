<?php

namespace Anny\Integrations\Models;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Contracts\WebhookCall;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Integrations;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * App\Models\IntegrationWebhookCall
 *
 * @property int $id
 * @property int $integration_id
 * @property int|null $subscription_id
 * @property string|null $external_id
 * @property string $type
 * @property string $url
 * @property mixed|null $headers
 * @property array|null $payload
 * @property string|null $exception
 * @property int|null $failed_runs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Integration $integration
 * @property-read \App\Models\IntegrationWebhookSubscription|null $subscription
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IntegrationWebhookCall newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereFailedRuns($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereHeaders($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereSubscriptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookCall whereUrl($value)
 * @mixin \Eloquent
 */
class IntegrationWebhookCall extends Model implements WebhookCall
{
    protected $guarded = [];

    protected $casts = [
        'integration_id' => 'int',
        'payload' => 'array',
        'headers' => AsArrayObject::class,
    ];

    /**
     * @return BelongsTo
     */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integrations::$model);
    }

    /**
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Integrations::$webhookSubscriptionModel, 'subscription_id');
    }

    public function getIntegration(): IntegrationModel|Model
    {
        return $this->integration;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param Request                                    $request
     * @param \App\Models\IntegrationWebhookSubscription $webhookSubscription
     * @param array                                      $attributes
     *
     * @return IntegrationWebhookCall|Model
     */
    public static function createFromRequest(Request $request, \App\Models\IntegrationWebhookSubscription $webhookSubscription, array $attributes)
    {
        $headers = self::headersToStore($webhookSubscription, $request);

        $webhookAttributes = array_merge([
            'integration_id' => $webhookSubscription->integration_id,
            'subscription_id' => $webhookSubscription->id,
            'url' => $request->fullUrl(),
            'headers' => $headers,
            'payload' => $request->input()
        ], $attributes);

        return self::create($webhookAttributes);
    }

    /**
     *
     * @param WebhookSubscription $webhookSubscription
     * @param Request             $request
     *
     * @return array
     */
    public static function headersToStore(WebhookSubscription $webhookSubscription, Request $request): array
    {
        $headerNamesToStore = '*';

        if ($headerNamesToStore === '*') {
            return $request->headers->all();
        }

        $headerNamesToStore = array_map(
            fn (string $headerName) => strtolower($headerName),
            $headerNamesToStore,
        );

        return collect($request->headers->all())
            ->filter(fn (array $headerValue, string $headerName) => in_array($headerName, $headerNamesToStore))
            ->toArray();
    }

    public function headerBag(): HeaderBag
    {
        return new HeaderBag($this->headers ?? []);
    }

    public function headers(): HeaderBag
    {
        return $this->headerBag();
    }

    public function saveException(Exception $exception): self
    {
        $this->exception = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        $this->save();

        return $this;
    }

    public function clearException(): self
    {
        $this->exception = null;

        $this->save();

        return $this;
    }
}