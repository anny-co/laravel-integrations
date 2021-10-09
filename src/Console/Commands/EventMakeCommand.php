<?php


namespace Anny\Integrations\Console\Commands;

class EventMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'event';
}