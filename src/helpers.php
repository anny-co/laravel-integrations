<?php

if (!function_exists('integrations'))
{
	/**
	 * Get integrations manager.
	 *
	 * @return \Bddy\Integrations\IntegrationsRegistry
	 */
	function integrations()
	{
		/** @var \Bddy\Integrations\IntegrationsRegistry $integrations */
		$integrations = app('integrations');

		return $integrations;
	}
}