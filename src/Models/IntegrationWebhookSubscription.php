<?php

namespace Anny\Integrations\Models;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Contracts\WebhookSubscription;
use Anny\Integrations\Integrations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Integrations\Webhooks\WebhookSubscription.
 *
 * @property int $id
 * @property string|null $integration
 * @property string|null $external_id
 * @property string|null $secret
 * @property string|null $url
 * @property array|null $data
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|IntegrationWebhookSubscription newModelQuery()
 * @method static Builder|IntegrationWebhookSubscription newQuery()
 * @method static Builder|IntegrationWebhookSubscription query()
 * @method static Builder|IntegrationWebhookSubscription whereCreatedAt($value)
 * @method static Builder|IntegrationWebhookSubscription whereData($value)
 * @method static Builder|IntegrationWebhookSubscription whereId($value)
 * @method static Builder|IntegrationWebhookSubscription whereMetaTag($value)
 * @method static Builder|IntegrationWebhookSubscription whereIntegration($value)
 * @method static Builder|IntegrationWebhookSubscription whereRegistrationId($value)
 * @method static Builder|IntegrationWebhookSubscription whereUpdatedAt($value)
 * @method static Builder|IntegrationWebhookSubscription whereUrl($value)
 * @mixin Eloquent
 */
class IntegrationWebhookSubscription extends Model implements WebhookSubscription
{
    protected $guarded = [];

    protected $casts = [
        'integration_id' => 'int',
        'data' => 'array',
        'secret' => 'encrypted',
        'expired_at' => 'datetime'
    ];

    /**
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            if(!$model->uuid) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * @return BelongsTo
     */
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integrations::$model);
    }

    /**
     * @return IntegrationModel|Model
     */
    public function getIntegration(): IntegrationModel|Model
    {
        return $this->integration;
    }

    /**
     * @return string|null
     */
    public function getSecret()
    {
        return $this->secret;
    }
}