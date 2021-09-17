<?php

namespace Bddy\Integrations\Http\Controllers;

use Bddy\Integrations\Contracts\HasAuthenticationStrategies;
use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Integrations;
use Bddy\Integrations\IntegrationsRegistry;

class OAuth2Controller
{
    /**
     * Redirect user to integration oauth2 authorization page.
     *
     * @param $uuid
     *
     * @return mixed
     */
    public function redirect($uuid)
    {
        /** @var IntegrationModel $integration */
        $integration = Integrations::newModel()
            ->newQuery()
            ->where('uuid', $uuid)
            ->firstOrFail();

        /** @var HasAuthenticationStrategies $manager */
        $manager = $integration->getIntegrationManager();

        return $manager->for($integration)->handleOAuth2Redirect();
    }

    /**
     * Handle callback from oauth2 code flow.
     *
     * @param $key
     */
    public function callback($key)
    {
        $manager = integrations()->getIntegrationManager($key);

        if(!$manager) {
            abort(404);
        }

        $manager->handleOAuth2Callback();
    }
}