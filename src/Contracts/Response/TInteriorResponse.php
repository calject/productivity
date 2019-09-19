<?php
/**
 * Author: 沧澜
 * Date: 2019-09-19
 */

namespace CalJect\Productivity\Contracts\Response;


use CalJect\Productivity\Components\Responses\InteriorResponse;

trait TInteriorResponse
{
    /**
     * @param InteriorResponse $response
     * @return static
     */
    public static function convertByResponse(InteriorResponse $response)
    {
        return static::make($response->getCode(), $response->getMessage(), $response->getData(), $response->getExpand())
            ->setIsSuccess($response->isSuccess())
            ->setHttpStatusCode($response->getHttpStatusCode())
            ->setException($response->getException());
    }
}