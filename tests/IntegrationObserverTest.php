<?php

namespace Anny\Integrations\Tests;

use Anny\Integrations\Models\Integration;
use Anny\Integrations\Tests\Stubs\ObserverIntegrationManager;
use Mockery\MockInterface;

class IntegrationObserverTest extends TestCase
{
    protected $managerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->managerMock = $this->partialMock(ObserverIntegrationManager::class, function (MockInterface $mock) {
            $mock->shouldReceive('saving')->once();
            $mock->shouldReceive('saved')->once();
            $mock->shouldReceive('creating')->once();
            $mock->shouldReceive('created')->once();

        });

        integrations()->registerIntegrationManager($this->managerMock);
    }

    /** @test */
    public function it_calls_hooks()
    {
        $integration = new Integration([
            'name' => 'test',
            'key' => ObserverIntegrationManager::getIntegrationKey(),
            'version' => 'v1.0',
            'model_type' => 'owner',
            'model_id' => '1',
            'settings' => []
        ]);

        $integration->save();

        $this->managerMock->shouldHaveReceived('saving');
        $this->managerMock->shouldHaveReceived('saved');
        $this->managerMock->shouldHaveReceived('creating');
        $this->managerMock->shouldHaveReceived('created');
    }
}