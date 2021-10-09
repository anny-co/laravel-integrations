<?php

namespace Anny\Integrations\Contracts;

interface ShouldEncryptSettings
{

    /**
     * @return array
     */
    public function getEncryptedSettingKeys(): array;
}