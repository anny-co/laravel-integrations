<?php

namespace Anny\Integrations\Tests\Stubs;

use Anny\Integrations\Support\AbstractIntegrationManager;

class ObserverIntegrationManager extends AbstractIntegrationManager
{

    protected static string $integrationKey = 'observer';

    protected static string $manifest = ObserverIntegrationManifest::class;

    public function getPossibleAuthenticationStrategies(): array
    {
        return [];
    }

    public function testConnection(): bool
    {
        return true;
    }

    public function getDefaultSettings(): array
    {
        return [];
    }

    public function rules(): array
    {
        return [];
    }

    public function settingRules(): array
    {
        return [];
    }

    public function saving()
    {

    }

    public function saved()
    {

    }

    public function creating()
    {

    }

    public function created()
    {

    }
}