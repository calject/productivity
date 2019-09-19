<?php
/**
 * Author: 沧澜
 * Date: 2019-09-17
 */

namespace CalJect\Productivity\Components\Http\Client;


use CalJect\Productivity\Components\Http\Curl\Curl;
use CalJect\Productivity\Contracts\ArrayConvertInterface;
use CalJect\Productivity\Contracts\ToArray;

class HttpRequest implements ToArray, ArrayConvertInterface
{
    /**
     * @var string
     */
    protected $url;
    
    /**
     * @var array
     */
    protected $header = [];
    
    /**
     * @var array|string
     */
    protected $body = [];
    
    /**
     * @var string
     */
    protected $method = Curl::POST;
    
    /**
     * @var int
     */
    protected $timeout = 15;
    
    /**
     * @param string $url
     * @return $this
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }
    
    /**
     * @param array $header
     * @return $this
     */
    public function setHeader(array $header)
    {
        $this->header = $header;
        return $this;
    }
    
    /**
     * @param array|string $body
     * @return $this
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
    
    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }
    
    /**
     * @param int $timeout
     * @return $this
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
    
    /**
     * @return array
     */
    public function getHeader(): array
    {
        return $this->header;
    }
    
    /**
     * @return array|string
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
    
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'header' => $this->header,
            'body' => $this->body,
            'timeout' => $this->timeout,
            'method' => $this->method
        ];
    }
    
    /**
     * @return array
     */
    public function __invoke()
    {
        return $this->toArray();
    }
    
    /**
     * @param array $arr
     * @return mixed
     */
    public function arrayConvert(array $arr)
    {
        foreach ($arr as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }
}