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
 * App\Models\IntegrationWebhookSubscription
 *
 * @property int $id
 * @property string $uuid
 * @property int $integration_id
 * @property mixed $secret
 * @property int $active
 * @property string|null $external_id
 * @property array|null $data
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $url
 * @property-read \App\Integration $integration
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\IntegrationWebhookSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereIntegrationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IntegrationWebhookSubscription whereUuid($value)
 * @mixin \Eloquent
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