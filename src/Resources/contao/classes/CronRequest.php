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
            $this->responseBody = "Request Exception:<br>The PHP flag 'allow_url_fopen' is not set.<br>The PHP 'cURL' extension is not available.<br>One is necessary.";
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
            $this->responseBody = "Request Exception:<br>".$t->getMessage();
            return 500;
        } catch (\Exception $e) {
            // Executed only in PHP 5.x, will not be reached in PHP 7
            $this->responseBody = "Request Exception:<br>".$e->getMessage();
            return 500;
        }
        $this->responseBody = $response->getBody(); 
        return $response->getStatusCode(); 
    }
    
    /**
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

