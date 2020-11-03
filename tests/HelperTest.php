<?php


namespace Bddy\Integrations\Tests;


use Bddy\Integrations\Contracts\IntegrationsRegistry;

class HelperTest extends TestCase
{

	public function test_helper_returns_integrations_manager()
	{
		$manager = integrations();

		$this->assertInstanceOf(IntegrationsRegistry::class, $manager);
	}
}