<?php

if (!function_exists('integrations'))
{
	/**
	 * Get integrations manager.
	 *
	 * @return \Anny\Integrations\IntegrationsRegistry
	 */
	function integrations()
	{
		/** @var \Anny\Integrations\IntegrationsRegistry $integrations */
		$integrations = app('integrations');

		return $integrations;
	}
}