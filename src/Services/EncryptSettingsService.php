<?php

namespace Bddy\Integrations\Services;

use Bddy\Integrations\Contracts\EncryptSettingsService as Contract;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;

class EncryptSettingsService implements Contract
{
    /**
     * @param array $settings
     * @param array $keys
     *
     * @return array
     */
    public function encryptSettings(array $settings, array $keys): array
    {
        $dottedKeys = Arr::dot($settings);
        foreach ($dottedKeys as $dottedKey => $value) {
            foreach ($keys as $key) {
                if (fnmatch($key, $dottedKey)) {
                    Arr::set($settings, $dottedKey, Crypt::encrypt($value));
                }
            }
        }

        return $settings;
    }

    /**
     * @param array $settings
     * @param array $keys
     *
     * @return array
     */
    public function decryptSettings(array $settings, array $keys): array
    {
        $dottedKeys = Arr::dot($settings);
        foreach ($dottedKeys as $dottedKey => $value) {
            foreach ($keys as $key) {
                if (fnmatch($key, $dottedKey)) {
                    Arr::set($settings, $dottedKey, Crypt::decrypt($value));
                }
            }
        }

        return $settings;
    }
}