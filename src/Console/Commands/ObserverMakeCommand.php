<?php


namespace Bddy\Integrations\Console\Commands;

class ObserverMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'observer';
}