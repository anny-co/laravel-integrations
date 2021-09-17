<?php

namespace Bddy\Integrations\Auth;

use Bddy\Integrations\Contracts\AuthenticationStrategy;
use Bddy\Integrations\Contracts\IntegrationModel;
use Bddy\Integrations\Events\RefreshingTokenFailed;
use Bddy\Integrations\Exceptions\IntegrationIsLockedException;
use Bddy\Integrations\Exceptions\InvalidStateException;
use Bddy\Integrations\Exceptions\RefreshTokenFailedException;
use Carbon\Carbon;
use Illuminate\Contracts\Container\BindingResolutionException;
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

abstract class OAuth2AuthenticationStrategy implements AuthenticationStrategy
{

    /**
     * @var string
     */
    protected $providerClass = AbstractProvider::class;

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
     * @param IntegrationModel $integrationModel
     */
    public function __construct(protected IntegrationModel $integrationModel)
    {

    }

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
        $accessToken = $this->getAccessToken($integration);

        if(is_null($accessToken)) {
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
     * Return a http client to make request.
     *
     * @param IntegrationModel $integration
     *
     * @return PendingRequest
     */
    public function getHttpClient(IntegrationModel $integration): PendingRequest
    {
        return Http::withHeaders(
            $this->getHttpHeaders($integration)
        )->withOptions(
            $this->getHttpOptions($integration)
        );
    }

    /**
     * Return headers for http client.
     *
     * @param IntegrationModel $integration
     *
     * @return array
     */
    public function getHttpHeaders(IntegrationModel $integration): array
    {
        $accessToken = $this->getAccessToken($integration);

        return [
            'Authorization' => 'Bearer ' . $accessToken,
            'Accept'        => 'application/json',
        ];
    }

    /**
     * Return options for http client.
     *
     * @param IntegrationModel $integration
     *
     * @return array
     */
    public function getHttpOptions(IntegrationModel $integration)
    {
        return [
            'base_uri' => $this->getHttpClientBaseUrl($integration)
        ];
    }

    /**
     * Return base uri to integration api.
     *
     * @param IntegrationModel $integration
     *
     * @return string
     */
    protected abstract function getHttpClientBaseUrl(IntegrationModel $integration): string;

    /**
     * @param IntegrationModel $integration
     *
     * @return PendingRequest
     */
    public function getUnauthenticatedClient(IntegrationModel $integration): PendingRequest
    {
        return Http::withOptions(
            $this->getHttpOptions($integration)
        )->withHeaders([
            'Accept' => 'application/json',
        ]);
    }

    /**
     * Redirect request to oauth2 page of integration to authorize it.
     *
     * @param IntegrationModel $integration
     *
     * @return RedirectResponse
     * @throws BindingResolutionException
     */
    public function redirect(IntegrationModel $integration)
    {
        $socialiteProvider = $this->getSocialiteProvider($integration);

        if ($this->usesPKCE)
        {
            $socialiteProvider->enablePKCE();
        }

        $socialiteProvider->scopes($this->scopes);

        return $socialiteProvider->redirect();
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
                    'query' => $this->getRefreshTokenFields($integration),
                    'debug' => $this->shouldDebug()
                ]
            )->post($this->getRefreshTokenUrl());
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
            'refresh_token' => Arr::get($secrets, 'refresh_token')
        ];
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

        return Socialite::driver($key);
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

        $socialite->extend($key, function (SocialiteManager $manager) use ($provider, $key) {
            return $manager->buildProvider($provider, config("integrations.${key}"));
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
    }

    /**
     * Get access token.
     *
     * @param IntegrationModel $integration
     *
     * @return string|null
     */
    public function getAccessToken(IntegrationModel $integration): string|null
    {
        return Arr::get($integration->getSecrets(), 'access_token');
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
        return $this->getExpiresAt($integration)?->subMinutes(5)->isBefore(now());
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