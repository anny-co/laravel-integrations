<?php

namespace Anny\Integrations\Tests\Stubs;

use Anny\Integrations\Support\AbstractIntegrationManager;
use Anny\Integrations\Support\IntegrationManifest;
use JetBrains\PhpStorm\Pure;

class ExampleIntegrationManager extends AbstractIntegrationManager
{

	/**
	 * Key of integration.
	 */
	protected static string $integrationKey = 'example';

	protected static string $manifest = ExampleIntegrationManifest::class;

	public function getDefaultSettings(): array
	{
		return [
			'settingA' => true,
			'settingB' => true,
		];
	}

    public function rules(): array
	{
		return [];
	}

	public function settingRules(): array
	{
		return [
			'settingA' => 'boolean|nullable',
			'settingB' => 'boolean|nullable'
		];
	}

    public function getPossibleAuthenticationStrategies(): array
    {
        return [];
    }

    public function testConnection(): bool
    {
       return true;
    }
}