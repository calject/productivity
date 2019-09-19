<?php
/**
 * Author: 沧澜
 * Date: 2019-09-19
 */

namespace CalJect\Productivity\Components\Responses;


use CalJect\Productivity\Contracts\Response\TInteriorResponseObjectConvert;

class InteriorResponseObject extends InteriorResponse
{
    use TInteriorResponseObjectConvert;
    /**
     * @var mixed
     */
    protected $requestObject;
    
    /**
     * @var mixed
     */
    protected $responseObject;
    
    /**
     * @param mixed $requestObject
     * @return $this
     */
    public function setRequestObject($requestObject)
    {
        $this->requestObject = $requestObject;
        return $this;
    }
    
    /**
     * @param mixed $responseObject
     * @return $this
     */
    public function setResponseObject($responseObject)
    {
        $this->responseObject = $responseObject;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getRequestObject()
    {
        return $this->requestObject;
    }
    
    /**
     * @return mixed
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
    
}