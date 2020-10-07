<?php


namespace Bddy\Integrations\Contracts;


interface Integration
{

	/**
	 * Returns the identifier name for this integration.
	 *
	 * @returns string
	 */
	public function getKey(): string;

	/**
	 * Returns the default config for an integration.
	 *
	 * @return array
	 */
	public function getDefaultSettings(): array;

}