<?php


namespace Bddy\Integrations\Console\Commands;

class ResourceMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'resource';

	/**
	 * Directory in which the resource is located.
	 * @var string
	 */
	protected string $directory = 'Http/Resources';
}