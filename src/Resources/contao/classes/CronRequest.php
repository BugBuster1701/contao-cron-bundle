<?php

namespace BugBuster\Cron;

use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Message\RequestFactory;


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
        $this->httpClient     = $httpClient     ?: HttpClientDiscovery::find();
        $this->requestFactory = $requestFactory ?: MessageFactoryDiscovery::find();
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
        $response = $this->httpClient->sendRequest($request);
        return $response->getStatusCode(); 
    }

}

