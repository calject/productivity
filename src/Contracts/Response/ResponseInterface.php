<?php
/**
 * Author: 沧澜
 * Date: 2019-09-19
 */

namespace CalJect\Productivity\Contracts\Response;


interface ResponseInterface
{
    /**
     * @return mixed
     */
    public function getCode();
    
    /**
     * @return string
     */
    public function getMessage(): string ;
    
    /**
     * @return mixed
     */
    public function getData();
}