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
        $config = ['timeout' => 5];
        $request = $this->requestFactory->createRequest('GET', $this->url);
        $response = $this->httpClient->sendRequest($request,$config);
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

