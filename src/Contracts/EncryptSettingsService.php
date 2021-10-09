<?php

namespace Anny\Integrations\Contracts;

interface EncryptSettingsService
{
    /**
     * @param array $settings
     * @param array $keys
     *
     * @return array
     */
    public function encryptSettings(array $settings, array $keys): array;

    /**
     * @param array $settings
     * @param array $keys
     *
     * @return array
     */
    public function decryptSettings(array $settings, array $keys): array;
}