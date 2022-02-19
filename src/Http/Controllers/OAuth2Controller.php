<?php

namespace Anny\Integrations\Http\Controllers;

use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Integrations;
use Illuminate\Http\Request;

class OAuth2Controller
{
    /**
     * Redirect user to integration oauth2 authorization page.
     *
     * @param Request $request
     * @param         $uuid
     *
     * @return mixed
     */
    public function redirect(Request $request, $uuid)
    {
        /** @var IntegrationModel $integration */
        $integration = Integrations::newModel()
            ->newQuery()
            ->where('uuid', $uuid)
            ->firstOrFail();

        return $integration->getIntegrationManager()->handleOAuth2Redirect($request);
    }

    /**
     * Handle callback from oauth2 code flow.
     *
     * @param Request $request
     * @param         $key
     */
    public function callback(Request $request, $key)
    {
        $manager = integrations()->getIntegrationManager($key);

        if(!$manager) {
            abort(404);
        }

        $manager->handleOAuth2Callback($request);

        if(method_exists($manager, 'authorized')) {
            $manager->authorized($manager->getIntegrationModel());
        }

        return view('anny::callback');
    }
}