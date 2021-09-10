<?php

namespace Bddy\Integrations\Tests\Stubs;

use Bddy\Integrations\Contracts\ShouldEncryptSettings;
use Bddy\Integrations\Support\AbstractIntegrationManager;
use Bddy\Integrations\Support\IntegrationManifest;

class EncryptionIntegrationManager extends AbstractIntegrationManager implements ShouldEncryptSettings
{

	/**
	 * Key of integration.
	 */
	protected static string $integrationKey = 'encryption_example';

	protected static string $manifest = EncryptionIntegrationManifest::class;

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
		return [
			'encrypted_setting_a' => 'string|nullable',
			'encrypted_setting_b' => 'string|nullable',
			'setting_c' => 'string|nullable'
		];
	}

    public function getEncryptedSettingKeys(): array
    {
        return [
            'encrypted_setting_a',
        ];
    }
}