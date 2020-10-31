<?php


namespace Bddy\Integrations\Console\Commands;

class ProviderMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'provider';
}