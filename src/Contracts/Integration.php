<?php


namespace Bddy\Integrations\Contracts;


interface Integration
{

	/**
	 * Returns the identifier name for this integration
	 * @returns string
	 */
	public function getKey(): string;

	public function getDefaultSettings(): array;

}