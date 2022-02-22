<?php

namespace Anny\Integrations\Tests\Stubs;

use Anny\Integrations\Auth\AccessTokenAuthenticationStrategy;
use Anny\Integrations\Contracts\ShouldEncryptSettings;
use Anny\Integrations\Support\AbstractIntegrationManager;

class EncryptionIntegrationManager extends AbstractIntegrationManager implements ShouldEncryptSettings
{
	/**
	 * @inheritdoc
	 */
	protected static string $integrationKey = 'encryption_example';

    /**
     * @inheritdoc
     */
	protected static string $manifest = EncryptionIntegrationManifest::class;

    /**
     * @inheritdoc
     */
	public function getDefaultSettings(): array
	{
		return [];
	}

    /**
     * @inheritdoc
     */
	public function rules(): array
	{
		return [];
	}

    /**
     * @inheritdoc
     */
	public function settingRules(): array
	{
		return [
			'encrypted_setting_a' => 'string|nullable',
			'encrypted_setting_b' => 'string|nullable',
			'setting_c' => 'string|nullable'
		];
	}

    /**
     * @inheritdoc
     */
    public function getEncryptedSettingKeys(): array
    {
        return [
            'encrypted_setting_a',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getPossibleAuthenticationStrategies(): array
    {
        return [

        ];
    }

    public function testConnection(): bool
    {
        return true;
    }
}