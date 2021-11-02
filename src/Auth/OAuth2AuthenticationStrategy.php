<?php

namespace Anny\Integrations\Auth;

use Anny\Integrations\Contracts\AuthenticationStrategy;
use Anny\Integrations\Contracts\IntegrationModel;
use Anny\Integrations\Events\OAuth2CallbackFinished;
use Anny\Integrations\Events\RefreshingTokenFailed;
use Anny\Integrations\Exceptions\IntegrationIsLockedException;
use Anny\Integrations\Exceptions\InvalidStateException;
use Anny\Integrations\Exceptions\RefreshTokenFailedException;
use Anny\Integrations\Integrations;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Two\AbstractProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class OAuth2AuthenticationStrategy extends AbstractAuthenticationStrategy implements AuthenticationStrategy
{

    /**
     * Socialite oauth2 provider class.
     *
     * @var string
     */
    protected string $providerClass = AbstractProvider::class;

    /**
     * Indicates if PKCE should be used.
     *
     * @var bool
     */
    protected bool $usesPKCE = false;

    /**
     * Array with scope required for authentication.
     *
     * @var array
     */
    protected array $scopes = [];

    /**
     * The separating character for the requested scopes.
     *
     * @var string
     */
    protected $scopeSeparator = ',';

    /**
     * Identifier key for this authentication strategy.
     *
     * @var string
     */
    protected string $key = 'oauth2';

    /**
     * @param IntegrationModel $integration
     *
     * @return bool
     * @throws IntegrationIsLockedException
     */
    public function authenticate(IntegrationModel $integration): bool
    {
        // If it is still locked throw exception
        Log::info('Check integration lock.');
        if ($this->isLocked($integration))
        {
            Log::info('Integration is locked.');
            throw new IntegrationIsLockedException($integration);
        }

        // Check if we need to refresh token
        Log::info('Check if access token expires soon.');
        if ($this->accessTokenExpiresSoon($integration))
        {
            Log::info('Access token expires soon or is expired. Requesting a new one.');

            // Run action for refreshing token
            return $this->refreshToken($integration);
        }

        return true;
    }

    /**
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function isAuthenticated(IntegrationModel $integration): bool {
        if(!$this->hasRequiredData($integration)) {
            return false;
        }

        return !$this->accessTokenExpiresSoon($integration);
    }

    /**
     * Return provider to use with this strategy.
     *
     * @return string
     */
    public function getProvider(): string
    {
        return $this->providerClass;
    }

    /**
     * Redirect request to oauth2 page of integration to authorize it.
     *
     * @param IntegrationModel $integration
     *
     * @return RedirectResponse
     * @throws BindingResolutionException
     */
    public function redirect(Request $request, IntegrationModel $integration)
    {
        $socialiteProvider = $this->getSocialiteProvider($integration);

        // Create redirect response
        $redirectResponse = $socialiteProvider->redirect();

        // Session has a state, we need to combine it with the current integration
        $state = $request->session()->get('state');

        // Put integration uuid into session
        $request->session()->put(
            $this->getIntegrationSessionKey($state),
            $integration->getKey()
        );

        return $redirectResponse;
    }

    /**
     * Handle oauth2 callback
     *
     * @param Request          $request
     * @param IntegrationModel $integration
     *
     * @throws BindingResolutionException
     */
    public function callback(Request $request, IntegrationModel $integration)
    {
        $socialiteProvider = $this->getSocialiteProvider($integration);

        if ($this->hasInvalidState($request))
        {
            throw new InvalidStateException();
        }

        $accessTokenResponse = $socialiteProvider->getAccessTokenResponse(
            $this->getCode($request)
        );

        $this->saveAccessTokenResponse($integration, $accessTokenResponse);

        event(new OAuth2CallbackFinished($integration, $accessTokenResponse));

        return true;
    }

    /**
     * Returns session key to store integration in relation to state.
     *
     * @param string $state
     *
     * @return string
     */
    protected function getIntegrationSessionKey(string $state)
    {
        return "integration_${state}";
    }

    /**
     * @param string $state
     *
     * @return string
     */
    public static function getIntegrationSessionKeyStatic(string $state)
    {
        return "integration_${state}";
    }

    /**
     * @param Request $request
     * @param string  $state
     *
     * @return IntegrationModel|\Illuminate\Database\Eloquent\Collection|Model
     */
    public static function getIntegrationFromCallbackRequest(Request $request)
    {
        // Find integration which is assigned for current callback
        $state = $request->session()->get('state');

        if (!$state)
        {
            throw new InvalidStateException();
        }

        $sessionKey     = static::getIntegrationSessionKeyStatic($state);
        $integrationKey = $request->session()->pull($sessionKey);

        return Integrations::newModel()->newQuery()->findOrFail($integrationKey);
    }


    /**
     * Refresh the access token for an integration.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     * @throws IntegrationIsLockedException
     * @throws RefreshTokenFailedException
     */
    public function refreshToken(IntegrationModel $integration): bool
    {
        if ($this->isLocked($integration))
        {
            throw new IntegrationIsLockedException($integration);
        }

        $this->lock($integration);

        $response = $this->getRefreshTokenResponse($integration);

        // Check if request has failed
        if ($response->failed())
        {
            $this->unlock($integration);
            $this->handleFailedRefreshTokenResponse($integration, $response);
        }

        $this->saveAccessTokenResponse($integration, $response->json());

        $this->unlock($integration);

        return true;
    }

    /**
     * Handle failed refresh token flow
     * @param IntegrationModel $integration
     * @param Response         $response
     *
     * @throws RefreshTokenFailedException
     */
    public function handleFailedRefreshTokenResponse(IntegrationModel $integration, Response $response)
    {
        Log::info('Integration: Request for refreshing token failed.', ['status' => $response->status()]);
        $data = $response->json();
        Log::debug('Integration: debug response', $data);

        // Create error message
        $errorMsg = 'We could not refresh the token. Please login again. More information: ';
        $errorMsg .= ' ('.Arr::get($data, 'error', '-').')';

        // Create exception and save it
        $exception = new RefreshTokenFailedException($integration, $errorMsg, $response->status());
        $manager = $integration->getIntegrationManager();
        if(method_exists($manager, 'saveError')) {
            $manager->saveError($errorMsg, $exception);
        }

        // Fire event that refreshing failed
        event(new RefreshingTokenFailed($integration, $response));

        throw $exception;
    }

    /**
     * Get a http client for token requests.
     *
     * @return PendingRequest
     */
    public function getTokenHttpClient(): PendingRequest
    {
        return Http::withHeaders(
            $this->getTokenHttpHeaders()
        );
    }

    /**
     * Return an array with headers for token client.
     *
     * @return array
     */
    public function getTokenHttpHeaders(): array
    {
        return [
            'Accept' => 'application/json'
        ];
    }

    /**
     * Return refresh token response.
     *
     * @param IntegrationModel $integration
     *
     * @return Response
     */
    public function getRefreshTokenResponse(IntegrationModel $integration): Response
    {
        return $this->getTokenHttpClient()
            ->withOptions([
                    'debug' => $this->shouldDebug()
                ]
            )->asForm()->post(
                $this->getRefreshTokenUrl(),
                $this->getRefreshTokenFields($integration)
            );
    }

    /**
     * Return url to refresh token endpoint.
     *
     * @return string
     */
    public abstract function getRefreshTokenUrl(): string;

    /**
     * Get query fields for refresh token request.
     *
     * @param IntegrationModel $integration
     *
     * @return array
     */
    public function getRefreshTokenFields(IntegrationModel $integration): array
    {
        $secrets = $integration->getSecrets();

        return [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $this->getRefreshToken($integration),
            'scopes'        => $this->formatScopes($this->getScopes(), $this->scopeSeparator)
        ];
    }

    /**
     * Format the given scopes.
     *
     * @param  array  $scopes
     * @param  string  $scopeSeparator
     * @return string
     */
    protected function formatScopes(array $scopes, $scopeSeparator)
    {
        return implode($scopeSeparator, $scopes);
    }

    /**
     * Get the current scopes.
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param IntegrationModel $integration
     *
     * @return Provider|AbstractProvider
     * @throws BindingResolutionException
     */
    protected function getSocialiteProvider(IntegrationModel $integration): Provider|AbstractProvider
    {
        // Get manager and integration key
        $manager = $integration->getIntegrationManager();
        $key     = $manager->getKey();

        // Get oauth2 provider
        $this->extendSocialite(
            $key,
            $this->getProvider()
        );

        $socialiteProvider = Socialite::driver($key);

        if ($this->usesPKCE)
        {
            $socialiteProvider->enablePKCE();
        }

        $socialiteProvider->scopes($this->scopes);

        return $socialiteProvider;
    }

    /**
     * @param string $key
     * @param        $provider
     *
     * @throws BindingResolutionException
     */
    public function extendSocialite(string $key, $provider)
    {
        /** @var SocialiteManager $socialite */
        $socialite = app()->make(SocialiteFactory::class);

        $socialite->extend($key, function ($app) use ($socialite, $provider, $key) {
            return $socialite->buildProvider($provider, config("integrations.${key}"));
        });
    }

    /**
     * Get the code from the request.
     *
     * @param Request $request
     *
     * @return string
     */
    protected function getCode(Request $request): string
    {
        return $request->input('code');
    }

    /**
     * Determine if the current request / session has a mismatching "state".
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function hasInvalidState(Request $request): bool
    {
        $state = $request->session()->pull('state');

        return !(strlen($state) > 0 && $request->input('state') === $state);
    }


    /**
     * @param IntegrationModel $integration
     * @param array            $response
     */
    protected function saveAccessTokenResponse(IntegrationModel $integration, array $response)
    {
        // Set secrets
        $secrets                  = $integration->getSecrets();
        $secrets['access_token']  = Arr::get($response, 'access_token');
        $secrets['token_type']    = Arr::get($response, 'token_type');
        $secrets['refresh_token'] = Arr::get($response, 'refresh_token');

        $expiresIn             = Arr::get($response, 'expires_in');
        $secrets['expires_in'] = Arr::get($response, 'expires_in');
        $secrets['expires_at'] = now()->addSeconds($expiresIn)->toW3cString();

        // Save
        $integration->setSecrets($secrets);
        $integration->save();
    }

    /**
     * Get access token.
     *
     * @param IntegrationModel $integration
     *
     * @return string|null
     */
    public function getRefreshToken(IntegrationModel $integration): string|null
    {
        return Arr::get($integration->getSecrets(), 'refresh_token');
    }

    /**
     * Get carbon instance when access token expires.
     *
     * @param IntegrationModel $integration
     *
     * @return Carbon|null
     */
    public function getExpiresAt(IntegrationModel $integration): ?Carbon
    {
        $value = Arr::get($integration->getSecrets(), 'expires_at');

        if (!$value)
        {
            return null;
        }

        return Carbon::parse($value);
    }

    /**
     * Check if access token expires soon.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function accessTokenExpiresSoon(IntegrationModel $integration): bool
    {
        return $this->getExpiresAt($integration)?->subMinutes(5)->isBefore(now()) ?? false;
    }

    /**
     * @param IntegrationModel $integration
     *
     * @return string
     */
    public function getRefreshingTokenCacheKey(IntegrationModel $integration): string
    {
        $integrationManagerKey = $integration->getIntegrationManager()->getKey();
        $integrationKey        = $integration->getKey();

        return "integrations.${integrationManagerKey}.${integrationKey}";
    }

    /**
     * Lock integration for refreshing token.
     *
     * @param IntegrationModel $integration
     *
     * @return $this
     */
    public function lock(IntegrationModel $integration): static
    {
        Cache::put(
            $this->getRefreshingTokenCacheKey($integration),
            true,
            now()->addSeconds(30)
        );

        return $this;
    }

    /**
     * Unlock integration for refreshing token.
     *
     * @param IntegrationModel $integration
     *
     * @return $this
     */
    public function unlock(IntegrationModel $integration): static
    {
        Cache::forget(
            $this->getRefreshingTokenCacheKey($integration)
        );

        return $this;
    }

    /**
     * Check if integration is locked for refreshing token.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function isLocked(IntegrationModel $integration): bool
    {
        return Cache::has(
            $this->getRefreshingTokenCacheKey($integration)
        );
    }

    /**
     * Determine if we should debug request.
     *
     * @return false|resource
     */
    protected function shouldDebug()
    {
        if (env('APP_DEBUG'))
        {
            return fopen(storage_path('logs/integration.log'), 'a+');
        }

        return false;
    }
}