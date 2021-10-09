<?php


namespace Anny\Integrations\Console\Commands;

class ListenerMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'listener';
}