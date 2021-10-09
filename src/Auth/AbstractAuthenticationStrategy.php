<?php

namespace Anny\Integrations\Auth;

use Anny\Integrations\Contracts\AuthenticationStrategy;
use Anny\Integrations\Contracts\IntegrationModel;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class AbstractAuthenticationStrategy implements AuthenticationStrategy
{

    /**
     * Identifier key for this authentication strategy.
     *
     * @var string
     */
    protected string $key;

    /**
     * Return identifier key for authentication strategy.
     *
     * @return string
     */
    public function getKey(): string {
        return $this->key;
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
     * Check if the strategy has required data.
     *
     * @param IntegrationModel $integration
     *
     * @return bool
     */
    public function hasRequiredData(IntegrationModel $integration): bool
    {
        return !empty($this->getAccessToken($integration));
    }
}