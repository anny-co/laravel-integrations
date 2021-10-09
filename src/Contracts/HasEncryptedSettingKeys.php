<?php

namespace Anny\Integrations\Contracts;

interface HasEncryptedSettingKeys
{
    /**
     * Return an array of setting keys which should be encrypted in the database.
     *
     * @return array
     */
    public function getEncryptedSettingKeys(): array;
}