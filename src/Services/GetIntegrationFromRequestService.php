<?php

namespace Anny\Integrations\Services;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Integrations;
use Anny\Integrations\Traits\Makeable;
use Illuminate\Database\Eloquent\Model;

class GetIntegrationFromRequestService
{
    use Makeable;

    /**
     * @return IntegrationModel|Model|null
     */
    public function getIntegration(): IntegrationModel|Model|null
    {
        // Search for integration
        $uuid = request()->route('integration');

        if(!$uuid) {
            return null;
        }

        return Integrations::newModel()->query()->where('uuid', $uuid)->first();
    }
}