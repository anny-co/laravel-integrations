<?php

namespace Anny\Integrations;

use Anny\Integrations\Models\IntegrationWebhookCall;
use Anny\Integrations\Models\IntegrationWebhookSubscription;
use Illuminate\Database\Eloquent\Model;

class Integrations
{

    /**
     * Integration model class.
     *
     * @var string
     */
    public static string $model = \Anny\Integrations\Models\Integration::class;

    /**
     * Integration webhook class.
     *
     * @var string
     */
    public static string $webhookModel = IntegrationWebhookCall::class;

    /**
     * Integration webhook subscription class.
     *
     * @var string
     */
    public static string $webhookSubscriptionModel = IntegrationWebhookSubscription::class;

    /**
     * Flag if webhook renewal should run in cron.
     *
     * @var bool
     */
    public static bool $shouldRunWebhookSubscriptionRenewal = true;

    /**
     * Threshold for which an expiring webhook subscription would be renewed.
     *
     * @var int
     */
    public static int $webhookSubscriptionRenewalThreshold = 6;

    /**
     * Queue name of which the jobs should run.
     *
     * @var string
     */
    public static string $defaultQueue = 'default';

    /**
     * Create a new model from integration model.
     *
     * @return Model
     */
    public static function newModel(): Model
    {
        return new static::$model;
    }

    /**
     * Set model.
     *
     * @param string $integrationModel
     */
    public static function useModel(string $integrationModel)
    {
        static::$model = $integrationModel;
    }

    /**
     * Create a new model from integration model.
     *
     * @return Model
     */
    public static function newWebhookCallModel(): Model
    {
        return new static::$webhookModel;
    }

    /**
     * Set model.
     *
     * @param string $model
     */
    public static function useWebhookCallModel(string $model)
    {
        static::$webhookModel = $model;
    }

    /**
     * @param bool $shouldRun
     *
     * @return void
     */
    public static function setShouldRunWebhookSubscriptionRenewal(bool $shouldRun = true)
    {
        static::$shouldRunWebhookSubscriptionRenewal = $shouldRun;
    }

    /**
     * @param int $hours
     *
     * @return void
     */
    public static function useCustomWebhookSubscriptionRenewalThreshold(int $hours)
    {
        static::$webhookSubscriptionRenewalThreshold = $hours;
    }

    /**
     * @param string $queue
     * @return void
     */
    public static function setDefaultQueue(string $queue)
    {
        static::$defaultQueue = $queue;
    }

    /**
     * Create a new model from integration model.
     *
     * @return Model
     */
    public static function newWebhookSubscriptionModel(): Model
    {
        return new static::$webhookSubscriptionModel;
    }

    /**
     * Set model.
     *
     * @param string $model
     */
    public static function useWebhookSubscriptionModel(string $model)
    {
        static::$webhookSubscriptionModel = $model;
    }

    /**
     * Register routes for integrations.
     *
     * @param string $prefix
     *
     * @return IntegrationRouteRegistrar
     */
    public static function routes(string $prefix = 'integrations'): IntegrationRouteRegistrar
    {
        return (new IntegrationRouteRegistrar())->prefix($prefix);
    }
}