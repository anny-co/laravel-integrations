<?php


namespace Bddy\Integrations\Console\Commands;

class RuleMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'rule';
}