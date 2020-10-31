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