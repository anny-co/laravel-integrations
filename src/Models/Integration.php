<?php

namespace Bddy\Integrations\Models;

use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Traits\EncryptsSettings;
use Bddy\Integrations\Traits\IsIntegrationModel;
use Illuminate\Database\Eloquent\Model;

class Integration extends Model implements IntegrationModel
{
    use IsIntegrationModel;
    use EncryptsSettings;
}