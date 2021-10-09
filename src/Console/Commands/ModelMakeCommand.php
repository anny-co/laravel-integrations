<?php


namespace Anny\Integrations\Console\Commands;

class ModelMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'model';
}