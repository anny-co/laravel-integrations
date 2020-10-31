<?php


namespace Bddy\Integrations\Console\Commands;

class NotificationMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'notification';
}