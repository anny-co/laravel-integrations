<?php


namespace Bddy\Integrations\Console\Commands;

class CommandMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'command';

	/**
	 * Directory in which the resource is located.
	 * @var string
	 */
	protected string $directory = 'Console/Commands';
}