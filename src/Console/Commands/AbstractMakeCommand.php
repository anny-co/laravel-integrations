<?php


namespace Bddy\Integrations\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;

abstract class AbstractMakeCommand extends Command
{
	/**
	 * Define for which resource this make command is
	 * @var string
	 */
	protected string $resource = '';

	/**
	 * @var string
	 */
	protected string $directory = '';

	public function __construct()
	{
		// Setting properties from resource
		$this->name = "integration:make:{$this->resource}";
		$this->signature = "integration:make:{$this->resource} {integration} {name}";
		$this->description = "Create a new {$this->resource} class.";
		parent::__construct();
	}

	/**
	 * @return void
	 */
	public function handle()
	{
		// Make resource
		$this->call("make:{$this->resource}", [
			'name' => $this->getDirectoryName() . $this->getIntegrationFileName()
		]);
	}

	/**
	 * Return file name of resource.
	 *
	 * @return string
	 */
	protected function getIntegrationFileName() {
		$integration = $this->argument('name');
		return (string) Str::of($integration)->camel()->ucfirst();
	}

	protected function getResourceDir() {
		return $resourceDir = Str::of($this->resource)->camel()->plural()->ucfirst()->finish('/');
	}

	/**
	 * Return in which directory the resource should go in.
	 * @return \Illuminate\Support\Stringable|string
	 */
	protected function getDirectoryName() {
		if($this->directory === ''){
			// Make directory
			$integrationName = $integration = $this->argument('integration');
			$resourceDir = $this->getResourceDir();
			return "App/Integrations/{$integrationName}/{$resourceDir}";
		}

		return $this->directory;
	}
}