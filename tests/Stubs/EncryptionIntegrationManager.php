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

	public function getDefaultSettings(): array
	{
		return [];
	}

	public function rules()
	{
		return [];
	}

	public function settingRules()
	{
		return [
			'encrypted_setting_a' => 'string|nullable',
			'encrypted_setting_b' => 'string|nullable',
			'setting_c' => 'string|nullable'
		];
	}

    /**
     * @return IntegrationManifest
     */
    public function getManifest(): IntegrationManifest
    {
        return new EncryptionIntegrationManifest(
            'Encryption Integration',
            self::getIntegrationKey(),
            true,
                '',
            'This is an example Integration which uses encrypted settings.'
        );
    }

    public function getDefinitions(): array
    {
        return [];
    }

    public function getEncryptedSettingKeys(): array
    {
        return [
            'encrypted_setting_a',
        ];
    }
}