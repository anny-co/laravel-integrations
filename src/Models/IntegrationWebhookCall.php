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
 * \Anny\Integrations\Models\IntegrationWebhook.
 *
 * @mixin Builder
 * @property int $id
 * @property string|null $webhook_id
 * @property string|null $data
 * @property int|null $failed_runs
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|\Anny\Integrations\Models\IntegrationWebhookCall newModelQuery()
 * @method static Builder|IntegrationWebhookCall newQuery()
 * @method static Builder|IntegrationWebhookCall query()
 * @method static Builder|IntegrationWebhookCall whereCreatedAt($value)
 * @method static Builder|IntegrationWebhookCall whereData($value)
 * @method static Builder|IntegrationWebhookCall whereFailedRuns($value)
 * @method static Builder|IntegrationWebhookCall whereId($value)
 * @method static Builder|IntegrationWebhookCall whereIntegration($value)
 * @method static Builder|IntegrationWebhookCall whereUpdatedAt($value)
 * @method static Builder|IntegrationWebhookCall whereWebhookId($value)
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