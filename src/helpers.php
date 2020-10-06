<?php

if (!function_exists('integrations'))
{
	/**
	 * Get integrations manager.
	 *
	 * @return \Bddy\Integrations\IntegrationsManager
	 */
	function integrations()
	{
		/** @var \Bddy\Integrations\IntegrationsManager $integrations */
		$integrations = app('integrations');

		return $integrations;
	}
}