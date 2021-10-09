<?php


namespace Anny\Integrations\Console\Commands;

class RequestMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'request';

	/**
	 * Directory in which the resource is located.
	 * @var string
	 */
	protected string $directory = 'Http/Requests';
}