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

}