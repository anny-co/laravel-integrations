<?php


namespace Anny\Integrations\Tests;


use Anny\Integrations\Models\Integration;
use Anny\Integrations\Tests\Stubs\ExampleIntegrationManager;

class IntegrationManagerTest extends TestCase
{

	protected function setUp(): void
	{
		parent::setUp();
		$this->getRegistry()->registerIntegrationManager(new ExampleIntegrationManager());
	}

	public function testItReturnsDefaultSettings()
	{
		$integration = new Integration();
		$manager = ExampleIntegrationManager::get()->for($integration);

		$this->assertTrue($manager->setting('settingA'));
	}

	public function testItReturnsDefaultAsDefaultSetting()
	{
		$integration = new Integration();
		$manager = ExampleIntegrationManager::get()->for($integration);

		$this->assertFalse($manager->setting('settingC', false));
	}

	public function testItCanSetSettings()
	{
		$integration = new Integration();
		$manager = ExampleIntegrationManager::get()->for($integration);

		$manager->setting(['settingC'], 'test');

		$this->assertEquals('test', $integration->settings['settingC']);
	}
}