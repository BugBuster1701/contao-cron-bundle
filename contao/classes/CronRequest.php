<?php

/*
 * This file is part of a BugBuster Contao Bundle.
 *
 * @copyright  Glen Langer 2024 <http://contao.ninja>
 * @author     Glen Langer (BugBuster)
 * @package    Contao Cron Bundle
 * @link       https://github.com/BugBuster1701/contao-cron-bundle
 *
 * @license    LGPL-3.0-or-later
 */

namespace BugBuster\Cron;

use Contao\System;
use HttpClient\ResponseInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Request class, use symfony/http-client
 */
class CronRequest
{
	/**
	 * The URL to request.
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Timeout for HTTP Client
	 *
	 * @var int
	 */
	protected $timeout;

	/**
	 * The http client.
	 *
	 * @var HttpClient
	 */
	protected $httpClient;

	/**
	 * The http response body
	 *
	 * @var string
	 */
	protected $responseBody;

	/**
	 * The http response status code
	 *
	 * @var ResponseInterface
	 */
	protected $responseStatusCode;

	/**
	 * Create a new CronRequest instance.
	 *
	 * @param string $url
	 */
	public function __construct(string $url, int $timeout)
	{
		System::loadLanguageFile('tl_crontab');

		$this->responseBody = '';
		$this->url          = $url;
		$this->timeout      = $timeout;

		$this->httpClient   = HttpClient::create();

		if (false === $this->isCurlEnabled() && false === $this->isAllowUrlFopenEnabled())
		{
			$this->responseBody = "Request Exception:<br>" . $GLOBALS['TL_LANG']['tl_crontab']['allow_url_fopen_not_set'] .
													"<br>" . $GLOBALS['TL_LANG']['tl_crontab']['curl_not_available'] .
													"<br>" . $GLOBALS['TL_LANG']['tl_crontab']['one_is_necessary'];
			$this->responseStatusCode = 500;

			throw new \Exception($this->responseBody);
		}

		$this->checkTimeout();
	}

	/**
	 * Request
	 *
	 * @return integer HTTP Statuscode
	 */
	public function get()
	{
		try
		{
			$response = $this->httpClient->request('GET',
				html_entity_decode($this->url, ENT_COMPAT, System::getContainer()->getParameter('kernel.charset')),
				array('timeout' => $this->timeout)
			);
			$this->responseBody = $response->getContent();
		}
		catch (\Throwable $t)
		{
			$this->responseBody = "<span style='color:red;'>Request Exception:<br>" . $t->getMessage() . "</span>";
			$this->responseStatusCode = 500;

			return $this->responseStatusCode;
		}

		$this->responseStatusCode = $response->getStatusCode();

		return $this->responseStatusCode;
	}

	/**
	 * Get HTTP Status Code
	 *
	 * @return integer HTTP Status Code
	 */
	public function getResponseStatusCode()
	{
		return $this->responseStatusCode;
	}

	/**
	 * Get HTTP Response Body
	 *
	 * @return string HTTP Response Body
	 */
	public function getResponseBody()
	{
		return $this->responseBody;
	}

	/**
	 * Determinate if curl is enabled.
	 *
	 * @return bool
	 */
	public function isCurlEnabled()
	{
		return \function_exists('curl_init');
	}

	/**
	 * Determinate if allow_url_fopen is enabled.
	 *
	 * @return bool
	 */
	public function isAllowUrlFopenEnabled()
	{
		return (bool) \ini_get('allow_url_fopen');
	}

	public function checkTimeout()
	{
		if ($this->timeout < 5)
		{
			$this->timeout = 5;
		}
		if ($this->timeout > 300)
		{
			$this->timeout = 300;
		}
	}
}
