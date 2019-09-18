<?php
/**
 * Author: 沧澜
 * Date: 2019-09-16
 */

namespace CalJect\Productivity\Component\Http\Curl;

/**
 * Class CurlResponse
 * @package CalJect\Productivity\Component\Http\Curl
 */
class CurlResponse
{
    /**
     * http status
     * @var int
     */
    protected $statusCode = 0;
    
    /**
     * @var string
     */
    protected $reasonPhrase = '';
    
    /**
     * @var string
     */
    protected $protocol = '';
    
    /**
     * @var string
     */
    protected $responseData = '';
    
    /**
     * @var array
     */
    protected $responseHeader = [];
    
    /**
     * @var CurlInfo
     */
    protected $curlInfo;
    
    /**
     * @var int
     */
    protected $curlErrorNo = 0;
    
    /**
     * @var string
     */
    protected $curlErrorMsg = '';
    
    /*---------------------------------------------- set ----------------------------------------------*/
    
    /**
     * @param int $statusCode
     * @return $this
     */
    public function setStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    
    /**
     * @param string $reasonPhrase
     * @return $this
     */
    public function setReasonPhrase(string $reasonPhrase)
    {
        $this->reasonPhrase = $reasonPhrase;
        return $this;
    }
    
    /**
     * @param string $protocol
     * @return $this
     */
    public function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;
        return $this;
    }
    
    /**
     * @param string $responseData
     * @return $this
     */
    public function setResponseData(string $responseData)
    {
        $this->responseData = $responseData;
        return $this;
    }
    
    /**
     * @param array $responseHeader
     * @return $this
     */
    public function setResponseHeader(array $responseHeader)
    {
        $this->responseHeader = $responseHeader;
        return $this;
    }
    
    /**
     * @param CurlInfo $curlInfo
     * @return $this
     */
    public function setCurlInfo(CurlInfo $curlInfo)
    {
        $this->curlInfo = $curlInfo;
        return $this;
    }
    
    /**
     * @param int $curlErrorNo
     * @return $this
     */
    public function setCurlErrorNo(int $curlErrorNo)
    {
        $this->curlErrorNo = $curlErrorNo;
        return $this;
    }
    
    /**
     * @param string $curlErrorMsg
     * @return $this
     */
    public function setCurlErrorMsg(string $curlErrorMsg)
    {
        $this->curlErrorMsg = $curlErrorMsg;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
    
    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }
    
    /**
     * @return string
     */
    public function getResponseData(): string
    {
        return $this->responseData;
    }
    
    /**
     * @return array
     */
    public function getResponseHeader(): array
    {
        return $this->responseHeader;
    }
    
    /**
     * @return CurlInfo
     */
    public function getCurlInfo(): CurlInfo
    {
        return $this->curlInfo;
    }
    
    /**
     * @return int
     */
    public function getCurlErrorNo(): int
    {
        return $this->curlErrorNo;
    }
    
    /**
     * @return string
     */
    public function getCurlErrorMsg(): string
    {
        return $this->curlErrorMsg;
    }
    
}