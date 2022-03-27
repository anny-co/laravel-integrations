<?php

namespace Anny\Integrations\Support;

use Anny\Integrations\Auth\AbstractAuthenticationStrategy;
use Anny\Integrations\Auth\OAuth2AuthenticationStrategy;
use Anny\Integrations\Contracts\AuthenticationStrategy;
use Anny\Integrations\Contracts\HandlesErrorsAndFailures as HandlesErrorsAndFailuresContract;
use Anny\Integrations\Contracts\HasAuthenticationStrategies;
use Anny\Integrations\Contracts\HasIntegrations;
use Anny\Integrations\Contracts\IntegrationManager;
use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Contracts\WebhookProcessor;
use Anny\Integrations\Exceptions\MissingAuthenticationException;
use Anny\Integrations\Traits\HandlesErrorsAndFailures;
use Anny\Integrations\Traits\HasManifest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
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
     * @var string
     */
    protected static string $type = 'default';

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
     * @return static|IntegrationManager
     */
    public static function get(): static|IntegrationManager
    {
        return integrations()->getIntegrationManager(
            static::getIntegrationKey()
        );
    }

    /**
     * Get integration type.
     *
     * @return string
     */
    public static function getType(): string {
        return static::$type;
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
     * @return IntegrationModel|null
     */
    public function getIntegrationModel(): IntegrationModel|null
    {
        return $this->integration;
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
     * Returns http client for this integration.
     *
     * @return PendingRequest
     */
    public function httpClient(): PendingRequest
    {
        return $this->getSelectedAuthenticationStrategy()
            ? $this->getSelectedAuthenticationStrategy()->getHttpClient($this->integration)
            : Http::withOptions([]);
    }

    /**
     * Activate a specific integration model.
     *
     * @param IntegrationModel|null $integration
     *
     * @return static
     * @throws MissingAuthenticationException
     */
    public function activate(?IntegrationModel $integration): static
    {
        $this->for($integration);
        // Check if we can activate integration
        if(count($this->getPossibleAuthenticationStrategies()) <= 0) {
            $this->integration->active = true;

            return $this;
        }

        $selectedAuthenticationStrategy = $this->getSelectedAuthenticationStrategy();
        if(!$selectedAuthenticationStrategy || !$this->authenticationStrategyHasRequiredData()) {
            throw new MissingAuthenticationException();
        }

        $this->activating();

        $this->integration->active = true;

        return $this;
    }

    /**
     * @return void
     */
    public function activating()
    {
        //
    }

    /**
     * @return void
     */
    public function activated()
    {

    }


    /**
     * Deactivate a specific integration model.
     *
     * @param Model|IntegrationModel|null $integration
     *
     * @return static
     */
    public function deactivate(?IntegrationModel $integration): static
    {
        $this->for($integration);

        $this->deactivating();

        $integration->active = false;

        return $this;
    }

    /**
     * @return void
     */
    public function deactivating()
    {
        //
    }

    /**
     * @return void
     */
    public function deactivated()
    {
        //
    }


    /**
     * Initialize a specific integration model.
     *
     * @param Model|IntegrationModel|null $integration
     *
     * @return static
     */
    public function initialize(?IntegrationModel $integration): static
    {
        $this->for($integration);

        return $this;
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
     * Returns parser for webhooks.
     *
     * @return WebhookProcessor|null
     */
    public function getWebhookProcessor(): ?WebhookProcessor
    {
        return null;
    }

    /**
     * Return all possible authentication strategies.
     *
     * @return array
     */
    public abstract function getPossibleAuthenticationStrategies(): array;

    /**
     * Check if connection to integration is successful.
     *
     * @return bool
     */
    public abstract function testConnection(): bool;

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

        return $strategy->hasRequiredData($this->integration);
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

        return $strategy->authenticate($this->integration);
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
        return collect($this->getPossibleAuthenticationStrategies())
            ->first(fn(AuthenticationStrategy $authenticationStrategy) => $authenticationStrategy->getKey() === $key);
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