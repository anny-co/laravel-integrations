<?php

namespace Anny\Integrations\Models;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Traits\EncryptsSettings;
use Anny\Integrations\Traits\IsIntegrationModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Integration extends Model implements IntegrationModel
{
    use IsIntegrationModel;
    use EncryptsSettings;

    public function getSecrets(): array
    {
        return $this->secrets;
    }

    public function setSecrets(array $secrets): static
    {
        $this->secrets = $secrets;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return array|\ArrayAccess|mixed
     */
    public function getSecret(string $key, $default = null): mixed
    {
        return Arr::get($this->secrets, $key, $default);
    }

    /**
     * @param string $key
     * @param        $value
     */
    public function setSecret(string $key, $value): static
    {
        $secrets = $this->secrets;
        Arr::set($secrets, $key, $value);
        $this->secrets = $secrets;

        return $this;
    }
}