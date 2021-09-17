<?php

namespace Bddy\Integrations\Contracts;

use Bddy\Integrations\Auth\AbstractAuthenticationStrategy;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

interface HasAuthenticationStrategies
{

    /**
     * Return an array with all possible authentication methods
     *
     * @return array<string, AbstractAuthenticationStrategy>
     */
    public function getPossibleAuthenticationStrategies(): array;

    /**
     * Select a specific authentication strategy.
     *
     * @param AuthenticationStrategy $strategy
     *
     * @return void
     */
    public function selectAuthenticationStrategy(AuthenticationStrategy $strategy): void;

    /**
     * Return null or selected authentication strategy.
     *
     * @return AuthenticationStrategy|null
     */
    public function getSelectedAuthenticationStrategy(): AuthenticationStrategy|null;

    /**
     * Check if selected authentication strategy has all required data to perform authentication.
     *
     * @return bool
     */
    public function authenticationStrategyHasRequiredData(): bool;

    /**
     * Check if selected authentication strategy is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * Authenticate
     * @return mixed
     */
    public function authenticate();

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handleOAuth2Redirect(Request $request): RedirectResponse;

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function handleOAuth2Callback(Request $request);
}