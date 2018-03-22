<?php

namespace BugBuster\Cron;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;

/**
 * Request class, use httplug
 * 
 * @author Glen Langer (BugBuster)
 *        
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
     * The http client.
     *
     * @var \Http\Client\HttpClient
     */
    protected $httpClient;
    
    /**
     * The http request factory.
     *
     * @var \Http\Message\RequestFactory
     */
    protected $requestFactory;
    
    /**
     * The http response body
     * 
     * @var \Http\Message\MessageInterface
     */
    protected $responseBody;
    
    /**
     * The http response status code
     * 
     * @var \Http\Message\ResponseInterface
     */
    protected $responseStatusCode;
    
    /**
     * Create a new CronRequest instance.
     *
     * @param string $url
     * @param \Http\Client\HttpClient|null $httpClient
     * @param \Http\Message\RequestFactory|null $requestFactory
     *
     * @return void
     */
    public function __construct(string $url, HttpClient $httpClient = null, RequestFactory $requestFactory = null)
    {
        \System::loadLanguageFile('tl_crontab');
        
        $this->responseBody = '';
        $this->url          = $url;
        
        $this->httpClient     = $httpClient     ?: false;
        $this->requestFactory = $requestFactory ?: false;
        
        if (true === $this->isCurlEnabled() && false === $this->httpClient) 
        {
            $this->httpClient = \System::getContainer()->get('httplug.client.my_curl');
        }
        elseif (true === $this->isAllowUrlFopenEnabled() && false === $this->httpClient) 
        {
            $this->httpClient = \System::getContainer()->get('httplug.client.my_guzzle6');
        }
        else
        {
            $this->responseBody = "Request Exception:<br>".$GLOBALS['TL_LANG']['tl_crontab']['allow_url_fopen_not_set'].
                                                    "<br>".$GLOBALS['TL_LANG']['tl_crontab']['curl_not_available'].
                                                    "<br>".$GLOBALS['TL_LANG']['tl_crontab']['one_is_necessary'];
            $this->responseStatusCode = 500;
            throw new \Exception($this->responseBody);
        }
        $this->requestFactory = $requestFactory ?: \System::getContainer()->get('httplug.message_factory');
    }

    /**
     * Request
     *
     *
     * @return integer  HTTP Statuscode
     */
    public function get()
    {
        $request = $this->requestFactory->createRequest('GET', $this->url);
        try {
            $response = $this->httpClient->sendRequest($request);
        } catch (\Throwable $t) {
            // Executed only in PHP 7, will not match in PHP 5.x
            $this->responseBody = "<span style='color:red;'>Request Exception:<br>".$t->getMessage()."</span>";
            $this->responseStatusCode = 500;
            return $this->responseStatusCode;
        } catch (\Exception $e) {
            // Executed only in PHP 5.x, will not be reached in PHP 7
            $this->responseBody = "<span style='color:red;'>Request Exception:<br>".$e->getMessage()."</span>";
            $this->responseStatusCode = 500;
            return $this->responseStatusCode;
        }
        $this->responseBody       = $response->getBody(); 
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
        return function_exists('curl_init');
    }
    
    /**
     * Determinate if allow_url_fopen is enabled.
     *
     * @return bool
     */
    public function isAllowUrlFopenEnabled()
    {
        return (bool) ini_get('allow_url_fopen');
    }

}

