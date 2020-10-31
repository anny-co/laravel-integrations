<?php


namespace Bddy\Integrations\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

abstract class AbstractMakeCommand extends Command
{
	/**
	 * Define for which resource this make command is
	 * @var string
	 */
	protected string $resource = '';

	/**
	 * Directory in which the resource is located.
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
		// Get name
		$name = $this->getDirectoryName() . $this->getIntegrationFileName();

		// Get arguments
		$arguments = array_merge(
			$this->arguments(),
			['name' => $name]
		);

		unset($arguments['integration']);

		// Make resource
		$this->call("make:{$this->resource}", $arguments);
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

	/**
	 * Get resource directory
	 *
	 * @return string
	 */
	protected function getResourceDir() {
		if($this->directory === ''){
			return (string) Str::of($this->resource)->camel()->plural()->ucfirst()->finish('/');
		}else{
			return (string) Str::of($this->directory)->finish('/');
		}
	}

	/**
	 * Return in which directory the resource should go in.
	 * @return Stringable|string
	 */
	protected function getDirectoryName() {
		$integrationName = $this->argument('integration');

		return "App/Integrations/{$integrationName}/{$this->getResourceDir()}";
	}
}