<?php


namespace Bddy\Integrations\Tests;


use Illuminate\Support\Facades\File;

class CommandsTest extends TestCase
{

	public function test_it_makes_channels()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Broadcasting/TestChannel.php',
			'integration:make:channel Anny TestChannel'
		);
	}

	public function test_it_makes_casts()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Casts/TestCast.php',
			'integration:make:cast Anny TestCast'
		);
	}

	public function test_it_makes_commands()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Console/Commands/TestCommand.php',
			'integration:make:command Anny TestCommand'
		);
	}

	public function test_it_makes_controllers()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Http/Controllers/TestController.php',
			'integration:make:controller Anny TestController'
		);
	}

	public function test_it_makes_events()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Events/TestEvent.php',
			'integration:make:event Anny TestEvent'
		);
	}

	public function test_it_makes_exceptions()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Exceptions/TestException.php',
			'integration:make:exception Anny TestException'
		);
	}

	public function test_it_makes_jobs()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Jobs/TestJob.php',
			'integration:make:job Anny TestJob'
		);
	}

	public function test_it_makes_listeners()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Listeners/TestListener.php',
			'integration:make:listener Anny TestListener'
		);
	}

	public function test_it_makes_mails()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Mail/TestMail.php',
			'integration:make:mail Anny TestMail'
		);
	}

	public function test_it_makes_middleware()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Http/Middleware/TestMiddleware.php',
			'integration:make:middleware Anny TestMiddleware'
		);
	}

	public function test_it_makes_models()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Models/Anny.php',
			'integration:make:model Anny Anny'
		);
	}

	public function test_it_makes_notifications()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Notifications/TestNotification.php',
			'integration:make:notification Anny TestNotification'
		);
	}

	public function test_it_makes_observers()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Observers/TestObserver.php',
			'integration:make:observer Anny TestObserver'
		);
	}

	public function test_it_makes_policies()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Policies/TestPolicy.php',
			'integration:make:policy Anny TestPolicy'
		);
	}

	public function test_it_makes_providers()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Providers/TestProvider.php',
			'integration:make:provider Anny TestProvider'
		);
	}

	public function test_it_makes_requests()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Http/Requests/TestRequest.php',
			'integration:make:request Anny TestRequest'
		);
	}

	public function test_it_makes_resources()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Http/Resources/TestResource.php',
			'integration:make:resource Anny TestResource'
		);
	}

	public function test_it_makes_rules()
	{
		$this->runMakeCommandTest(
			'Integrations/Anny/Rules/TestRule.php',
			'integration:make:rule Anny TestRule'
		);
	}

	public function runMakeCommandTest(string $path, string $command)
	{
		// Remove previous created file
		$path = app_path($path);
		File::delete($path);

		// Run command
		$this->artisan($command)
			->assertExitCode(0)
			->run();

		// Check file exist
		$this->assertFileExists($path);
	}
}