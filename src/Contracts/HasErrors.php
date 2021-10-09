<?php


namespace Anny\Integrations\Contracts;


interface HasErrors
{
	/**
	 * Save an error to integration.
	 *
	 * @param string        $errorMessage
	 * @param \Throwable    $exception
	 * @param boolean|false $force
	 */
	public function saveError(string $errorMessage, \Throwable $exception, bool $force = false);

	/**
	 * Check if integration has an error.
	 *
	 * @return bool
	 */
	public function hasError(): bool;

	/**
	 * Remove error from integration
	 */
	public function removeError();
}