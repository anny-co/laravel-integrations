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

	/**
	 * Override default rules.
	 *
	 * @return array
	 */
	public function rules();

	/**
	 * Get rules for settings.
	 *
	 * @return array
	 */
	public function settingRules();

}