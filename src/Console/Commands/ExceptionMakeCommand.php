<?php


namespace Anny\Integrations\Console\Commands;

class ExceptionMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'exception';
}