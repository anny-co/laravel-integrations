<?php


namespace Bddy\Integrations\Tests;


use Illuminate\Support\Facades\File;

class CommandsTest extends TestCase
{
	public function test_it_makes_jobs()
	{
		// Remove previous created file
		$path = app_path('Integrations/Anny/Jobs/CreateUser.php');
		File::delete($path);

		// Run command
		$command = $this->artisan('integration:make:job Anny CreateUser')
			->assertExitCode(0);;

		$command->run();

		$this->assertFileExists($path);
	}
}