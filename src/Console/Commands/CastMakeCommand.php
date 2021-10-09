<?php


namespace Anny\Integrations\Console\Commands;

class CastMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'cast';
}