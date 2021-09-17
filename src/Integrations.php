<?php

namespace Bddy\Integrations;

use Illuminate\Database\Eloquent\Model;

class Integrations
{

    /**
     * Integration model class.
     *
     * @var string
     */
    public static $model = \Bddy\Integrations\Models\Integration::class;

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
}