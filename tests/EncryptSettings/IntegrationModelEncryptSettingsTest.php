<?php

namespace Bddy\Integrations\Tests\EncryptSettings;

use Bddy\Integrations\Models\Integration;
use Bddy\Integrations\Tests\Stubs\EncryptionIntegrationManager;
use Bddy\Integrations\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class IntegrationModelEncryptSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        integrations()->registerIntegrationManager(new EncryptionIntegrationManager());
    }

    /** @test */
    public function it_encrypts_settings_to_database()
    {
        $settings = ['encrypted_setting_a' => 'this_is_encrypted'];
        $integration = new Integration([
            'name' => 'test',
            'key' => EncryptionIntegrationManager::getIntegrationKey(),
            'version' => 'v1.0',
            'model_type' => 'owner',
            'model_id' => '1',
            'settings' => $settings
        ]);
        $integration->save();

        $this->assertDatabaseMissing('integrations', [
            'settings' => $settings,
        ]);

        // Check if decrypted  settings are the expected
        $row = DB::table('integrations')->select('settings')->first();
        $decryptedSettings = json_decode($row->settings, true);
        $decryptedSettings = Arr::set($rawSettings, 'encrypted_setting_a',
            Crypt::decrypt(Arr::get($decryptedSettings, 'encrypted_setting_a'))
        );

        $this->assertSame($settings, $decryptedSettings);
    }
}