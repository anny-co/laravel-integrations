<?php


namespace Anny\Integrations\Traits;


use GuzzleHttp\Client;

trait MakesClient
{

	/**
	 * Base uri of client
	 *
	 * @var string
	 */
	protected string $baseUri = '';

	/**
	 * Create guzzle client.
	 *
	 * @param string $baseUri
	 *
	 * @return Client
	 */
	protected function makeClient(string $baseUri = '')
	{
		if($baseUri === '') {
			$baseUri = $this->baseUri;
		}

		return new Client([
			'base_uri' => $baseUri,
			'headers'  => $this->getHeaders(),
		]);
	}

	/**
	 * Create headers for guzzle client.
	 *
	 * @return array
	 */
	protected function getHeaders()
	{
		return [];
	}
}