<?php

namespace Bddy\Integrations\Contracts;

interface ShouldEncryptSettings
{

    /**
     * @return array
     */
    public function getEncryptedSettingKeys(): array;
}