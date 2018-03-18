<?php

namespace BugBuster\Cron;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;
use Http\Client\Common\PluginClient;
use Http\Client\Common\Plugin\RedirectPlugin;

/**
 *
 * @author bibo
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
        $this->url = $url;
        
        $redirectPlugin = new RedirectPlugin();
        
        $this->httpClient     = $httpClient     ?: new PluginClient(HttpClientDiscovery::find(), [$redirectPlugin]);
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
        
        $this->responseBody = '';
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

}

