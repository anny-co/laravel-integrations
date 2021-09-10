<?php

namespace Bddy\Integrations\Tests\Stubs;

use Bddy\Integrations\Support\AbstractIntegrationManager;
use Bddy\Integrations\Support\IntegrationManifest;
use JetBrains\PhpStorm\Pure;

class ExampleIntegrationManager extends AbstractIntegrationManager
{

	/**
	 * Key of integration.
	 */
	protected static string $integrationKey = 'example';

	public function getDefaultSettings(): array
	{
		return [
			'settingA' => true,
			'settingB' => true,
		];
	}

	public function rules()
	{
		return [];
	}

	public function settingRules()
	{
		return [
			'settingA' => 'boolean|nullable',
			'settingB' => 'boolean|nullable'
		];
	}

    /**
     * @return IntegrationManifest
     */
    #[Pure] public function getManifest(): IntegrationManifest
    {
        return new ExampleIntegrationManifest(
            'Example Integration',
            self::getIntegrationKey(),
            true,
                '',
            'This is an example Integration.'
        );
    }

    public function getDefinitions(): array
    {
        return [];
    }
}