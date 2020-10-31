<?php


namespace Bddy\Integrations\Traits;


use GuzzleHttp\Client;

trait MakesClient
{
	/**
	 * @var Client|null
	 */
	protected ?Client $client = null;

	/**
	 * Create guzzle client.
	 *
	 * @param string $baseUri
	 */
	protected function makeClient(string $baseUri)
	{
		$this->client = new Client([
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