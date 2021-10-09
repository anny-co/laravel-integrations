<?php

namespace Anny\Integrations\Traits;

use Anny\Integrations\Contracts\EncryptSettingsService;
use Anny\Integrations\Contracts\ShouldEncryptSettings;
use ReflectionClass;

trait EncryptsSettings
{

    /**
     * Boot this trait and set up listeners.
     */
    public static function bootEncryptsSettings()
    {
        static::retrieved(function (self $integration) {
            $integration->decryptSettings();
        });

        static::saved(function (self $integration) {
            $integration->decryptSettings();
        });

        static::saving(function(self $integration) {
            $integration->encryptSettings();
        });
    }

    /**
     * Decrypt settings.
     */
    public function decryptSettings()
    {
        /** @var EncryptSettingsService $service */
        $service = app(EncryptSettingsService::class);

        $this->{$this->getSettingsKey()} = $service->decryptSettings(
            $this->getSettings(),
            $this->getEncryptedSettingKeys()
        );
    }

    /**
     * Encrypt settings.
     */
    public function encryptSettings()
    {
        /** @var EncryptSettingsService $service */
        $service = app(EncryptSettingsService::class);
        $this->{$this->getSettingsKey()} = $service->encryptSettings(
            $this->getSettings(),
            $this->getEncryptedSettingKeys()
        );
    }

    /**
     * Get array of settings.
     *
     * @return array
     */
    public function getSettings(): array
    {
        return $this->{$this->getSettingsKey()} ?? [];
    }

    /**
     * Get key to retrieve settings from model.
     *
     * @return string
     */
    public function getSettingsKey(): string
    {
        return 'settings';
    }

    /**
     * Return array of dotted keys which should be encrypted.
     *
     * @return array
     */
    public function getEncryptedSettingKeys(): array
    {
        // Check if static class has method to get integration manager
        if(method_exists(static::class, 'getIntegrationManager')) {
            $integrationManager = $this->getIntegrationManager();


            if($integrationManager instanceof ShouldEncryptSettings) {
                return $integrationManager->getEncryptedSettingKeys();
            }
        }

        return [];
    }

}