<?php

namespace Anny\Integrations\Traits;

use Anny\Integrations\Support\IntegrationManifest;

trait HasManifest
{
    /**
     * Class of the manifest.
     *
     * @var string
     */
    protected static string $manifest;

    /**
     * Get the class of the integrations manifest.
     *
     * @return string
     */
    public function getManifestClass(): string
    {
        return static::$manifest;
    }


    /**
     * Get connected manifest.
     *
     * @return IntegrationManifest
     */
    public function getManifest(): IntegrationManifest
    {
        /** @var IntegrationManifest $manifest */
        return new ($this->getManifestClass());
    }
}