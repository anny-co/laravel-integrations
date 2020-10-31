<?php


namespace Bddy\Integrations\Console\Commands;

class MailMakeCommand extends AbstractMakeCommand
{
	/**
	 * Define for which resource this make command is.
	 *
	 * @var string
	 */
	protected string $resource = 'mail';

	/**
	 * Directory in which the resource is located.
	 * @var string
	 */
	protected string $directory = 'Mail';
}