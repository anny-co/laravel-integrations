<?php

namespace Anny\Integrations\Traits;

trait Makeable
{
    /**
     * Make this service.
     *
     * @return static
     */
    public static function make(): static
    {
        return app(static::class);
    }
}