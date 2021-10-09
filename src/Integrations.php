<?php

namespace Anny\Integrations;

use Illuminate\Database\Eloquent\Model;

class Integrations
{

    /**
     * Integration model class.
     *
     * @var string
     */
    public static $model = \Anny\Integrations\Models\Integration::class;

    /**
     * Create a new model from integration model.
     *
     * @return Model
     */
    public static function newModel()
    {
        return new self::$model;
    }

    /**
     * Set model.
     *
     * @param mixed $integrationModel
     */
    public static function useModel(mixed $integrationModel)
    {
        self::$model = $integrationModel;
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