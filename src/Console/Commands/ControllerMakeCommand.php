<?php


namespace Bddy\Integrations\Console\Commands;

class ControllerMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'controller';

	/**
	 * Directory in which the resource is located.
	 * @var string
	 */
	protected string $directory = 'Http/Controllers';
}