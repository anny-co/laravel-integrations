<?php

namespace Anny\Integrations\Models;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Traits\EncryptsSettings;
use Anny\Integrations\Traits\IsIntegrationModel;
use Illuminate\Database\Eloquent\Model;

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
}