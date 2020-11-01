<?php


namespace Bddy\Integrations\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ActionMakeCommand extends AbstractMakeCommand
{

	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'action';
}