<?php

namespace Bddy\Integrations\Support;

use Bddy\Integrations\Auth\AbstractAuthenticationStrategy;
use Bddy\Integrations\Auth\OAuth2AuthenticationStrategy;
use Bddy\Integrations\Contracts\AuthenticationStrategy;
use Bddy\Integrations\Contracts\HandlesErrorsAndFailures as HandlesErrorsAndFailuresContract;
use Bddy\Integrations\Contracts\HasAuthenticationStrategies;
use Bddy\Integrations\Contracts\HasIntegrations;
use Bddy\Integrations\Contracts\IntegrationManager;
use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Traits\HandlesErrorsAndFailures;
use Bddy\Integrations\Traits\HasManifest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractIntegrationManager implements IntegrationManager, HandlesErrorsAndFailuresContract, HasAuthenticationStrategies
{
    use HasManifest;
    use HandlesErrorsAndFailures;

    /**
     * Key of integration.
     */
    protected static string $integrationKey;

    /**
     * Current integration model.
     * @var null|Model|IntegrationModel
     */
    protected $integration = null;

    /**
     *
     */
    const AUTHENTICATION_STRATEGY_SETTING_KEY = 'authentication_strategy';

    /**
     * Get instance from manager.
     *
     * @return IntegrationManager
     */
    public static function get(): static
    {
        return integrations()->getIntegrationManager(
            static::getIntegrationKey()
        );
    }

    /**
     * Set the model for which the next actions should be taken.
     *
     * @param Model|IntegrationModel|null $integration
     *
     * @return static
     */
    public function for(?IntegrationModel $integration = null): static
    {
        if ($integration)
        {
            $this->integration = $integration;
        }

        return $this;
    }


    /**
     * Return integration key.
     *
     * @return string
     */
    public static function getIntegrationKey(): string
    {
        return static::$integrationKey;
    }

    /**
     * Return integration key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return static::$integrationKey;
    }

    /**
     * Get specific setting of integration. It will retrieve a default setting when setting is not found and default is null.
     * If setting is not found and default is not null it will return default.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string|null $key
     * @param mixed|null        $default
     *
     * @return mixed
     */
    public function setting(array|string|null $key = null, mixed $default = null): mixed
    {
        // Return all settings
        if (is_null($key))
        {
            return $this->integration->settings;
        }

        // Set values
        if (is_array($key))
        {
            // Set each key
            $settings = $this->integration->settings;
            foreach ($key as $keyString => $value)
            {
                Arr::set($settings, $keyString, $value);
            }
            $this->integration->settings = $settings;

            return $default;
        }

        // Return specific setting
        $value = Arr::get($this->integration->settings, $key, $default);
        if (!$value)
        {
            // Return default from default settings
            return Arr::get($this->getDefaultSettings(), $key, $default);
        }

        return $value;
    }

    /**
     * Retrieve integration model from related model which owns the integration.
     *
     * @param Model|HasIntegrations $model
     *
     * @return Model|IntegrationModel|null
     */
    public function retrieveModelFrom(HasIntegrations $model): Model|IntegrationModel|null
    {
        return $model
            ->integrations()
            ->where('model_type', '=', $model->getMorphClass())
            ->where('model_id', '=', $model->getKey())
            ->where('key', '=', static::getIntegrationKey())
            ->first();
    }

    /**
     * Activate a specific integration model.
     *
     * @param Model|IntegrationModel|null $integration
     *
     * @return mixed
     */
    public function activate(?IntegrationModel $integration)
    {
        $this->for($integration);
        $integration->active = true;

        return $integration->save();
    }


    /**
     * Deactivate a specific integration model.
     *
     * @param Model|IntegrationModel|null $integration
     *
     * @return mixed
     */
    public function deactivate(?IntegrationModel $integration)
    {
        $this->for($integration);
        $integration->active = false;

        return $integration->save();
    }

    /**
     * Initialize a specific integration model.
     *
     * @param Model|IntegrationModel|null $integration
     *
     * @return mixed
     */
    public function initialize(?IntegrationModel $integration)
    {
        $this->for($integration);

        return $this;
    }

    /**
     * Updating a specific integration model.
     *
     * @param Model|IntegrationModel|null $integration
     * @param array                       $attributes
     *
     * @return mixed
     */
    public function updating(?IntegrationModel $integration, array $attributes)
    {
        $this->for($integration);

        return $attributes;
    }

    /**
     * Get connected manifest.
     *
     * @return IntegrationManifest
     */
    public function getManifest(): IntegrationManifest
    {
        /** @var IntegrationManifest $manifest */
        $manifest = new ($this->getManifestClass());

        // get possible authentication strategies
        $strategies = collect($this->getPossibleAuthenticationStrategies())
            ->map(fn(AbstractAuthenticationStrategy $strategy) => $strategy->getKey())->values()->toArray();

        return $manifest->setKey(
            self::getIntegrationKey()
        )->setAuthenticationStrategies(
            $strategies
        );
    }

    /**
     * Return all possible authentication strategies.
     *
     * @return array
     */
    public abstract function getPossibleAuthenticationStrategies(): array;

    /**
     * Select a specific authentication strategy.
     *
     * @param AuthenticationStrategy $strategy
     *
     * @return void
     */
    public function selectAuthenticationStrategy(AuthenticationStrategy $strategy): void
    {
        $this->setting([
            self::AUTHENTICATION_STRATEGY_SETTING_KEY => $strategy->getKey()
        ]);
    }

    /**
     * Return null or selected authentication strategy.
     *
     * @return AuthenticationStrategy|null
     */
    public function getSelectedAuthenticationStrategy(): AuthenticationStrategy|null
    {
        $key = $this->setting(self::AUTHENTICATION_STRATEGY_SETTING_KEY);
        // Find strategy by key
        return collect($this->getPossibleAuthenticationStrategies())
            ->first(fn(AuthenticationStrategy $strategy) => $strategy->getKey() === $key);
    }

    /**
     * Check if selected authentication strategy has all required data to perform authentication.
     *
     * @return bool
     */
    public function authenticationStrategyHasRequiredData(): bool
    {
        $strategy = $this->getSelectedAuthenticationStrategy();

        if (is_null($strategy))
        {
            return true;
        }

        return $strategy->hasRequiredData();
    }

    /**
     * Check if selected authentication strategy is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        $strategy = $this->getSelectedAuthenticationStrategy();

        if (is_null($strategy))
        {
            return true;
        }

        return $strategy->isAuthenticated($this->integration);
    }

    /**
     * Authenticate
     * @return mixed
     */
    public function authenticate()
    {
        $strategy = $this->getSelectedAuthenticationStrategy();

        $strategy->authenticate($this->integration);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleOAuth2Redirect(Request $request): RedirectResponse
    {
        // check if manager has oauth2 as authentication strategy
        $strategy = $this->getOauth2AuthenticationStrategy();

        // Create redirect response
        return $strategy->redirect($request, $this->integration);
    }

    /**
     * @param Request $request
     *
     * @return void
     */
    public function handleOAuth2Callback(Request $request)
    {
        // Set integration
        $integration = OAuth2AuthenticationStrategy::getIntegrationFromCallbackRequest($request);
        $this->for($integration);

        // Get strategy
        $strategy = $this->getOauth2AuthenticationStrategy();

        return $strategy->callback($request, $integration);
    }

    /**
     * @param string $key
     *
     * @return AuthenticationStrategy|null
     */
    public function getAuthenticationStrategy(string $key): ?AuthenticationStrategy
    {
        return collect($this->getPossibleAuthenticationMethods())->first(fn(AuthenticationStrategy $authenticationStrategy) => $authenticationStrategy->key() === $key);
    }

    /**
     * Return selected oauth2 strategy or fail.
     *
     * @return OAuth2AuthenticationStrategy|AuthenticationStrategy|mixed|null
     */
    protected function getOauth2AuthenticationStrategy()
    {
        $strategy = $this->getSelectedAuthenticationStrategy();
        if (!$strategy instanceof OAuth2AuthenticationStrategy)
        {
            // throw exception
            abort(404);
        }

        return $strategy;
    }
}