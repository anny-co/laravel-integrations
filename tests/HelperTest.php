<?php


namespace Anny\Integrations\Tests;


use Anny\Integrations\Contracts\IntegrationsRegistry;

class HelperTest extends TestCase
{

	public function test_helper_returns_integrations_manager()
	{
		$manager = integrations();

		$this->assertInstanceOf(IntegrationsRegistry::class, $manager);
	}
}