<?php

namespace Bddy\Integrations\Traits;

use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Services\GetIntegrationFromRequestService;
use Illuminate\Database\Eloquent\Model;

Trait GetsIntegrationFromRequest
{
    /**
     * @var Model|IntegrationModel|null
     */
    public Model|IntegrationModel|null $integration = null;

    /**
     * @var bool
     */
    public bool $integrationNotFound = false;

    /**
     * @return Model|IntegrationModel|null
     */
    public function getIntegration(): IntegrationModel|Model|null
    {
        // Check if we already got integration
        if(!is_null($this->integration)) {
            return $this->integration;
        }

        // Check if we already searched for integration
        if($this->integrationNotFound) {
            return null;
        }

        $service = GetIntegrationFromRequestService::make();

        $integration = $service->getIntegration();

        if(!$integration) {
            $this->integrationNotFound = true;
        }

        return $integration;
    }
}