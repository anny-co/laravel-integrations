<?php


namespace Bddy\Integrations\Console\Commands;

class ChannelMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'channel';

	/**
	 * Directory in which the resource is located.
	 * @var string
	 */
	protected string $directory = 'Broadcasting';
}