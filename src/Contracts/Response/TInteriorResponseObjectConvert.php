<?php
/**
 * Author: 沧澜
 * Date: 2019-09-19
 */

namespace CalJect\Productivity\Contracts\Response;

use CalJect\Productivity\Components\Responses\InteriorResponseObject;

trait TInteriorResponseObjectConvert
{
    use TInteriorResponse;
    
    /**
     * @param InteriorResponseObject $response
     * @return static
     */
    public static function convertByResponseObject(InteriorResponseObject $response)
    {
        return static::convertByResponse($response)
            ->setRequestObject($response->getRequestObject())
            ->setResponseObject($response->getResponseObject());
    }
}