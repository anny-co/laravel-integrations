<?php

namespace Bddy\Integrations\Tests\Stubs;

class ExampleIntegrationManager extends \Bddy\Integrations\Support\AbstractIntegrationManager
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

	public function getDefinitions(): array
	{
		return [
			'key' => self::getIntegrationKey(),
			'name' => 'Example Integrations',
			'description' => 'Desc'
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

}