<?php
/**
 * Author: 沧澜
 * Date: 2019-09-17
 */

namespace CalJect\Productivity\Components\Http\Client;


use CalJect\Productivity\Components\Http\Curl\CurlResponse;

class HttpResponse extends CurlResponse
{
    /**
     * @var HttpRequest
     */
    protected $request;
    
    /**
     * @param HttpRequest $request
     * @return $this
     */
    public function setRequest(HttpRequest $request)
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest
    {
        return $this->request;
    }
    
    /**
     * @param CurlResponse $curlResponse
     * @return static
     */
    public static function createByCurlResponse(CurlResponse $curlResponse)
    {
        $instance = new static();
        $instance->setStatusCode($curlResponse->getStatusCode());
        $instance->setProtocol($curlResponse->getProtocol());
        $instance->setReasonPhrase($curlResponse->getReasonPhrase());
        $instance->setResponseHeader($curlResponse->getResponseHeader());
        $instance->setResponseData($curlResponse->getResponseData());
        $instance->setCurlInfo($curlResponse->getCurlInfo());
        $instance->setCurlErrorNo($curlResponse->getCurlErrorNo());
        $instance->setCurlErrorMsg($curlResponse->getCurlErrorMsg());
        return $instance;
    }
    
}