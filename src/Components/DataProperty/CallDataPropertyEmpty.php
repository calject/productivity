<?php
/**
 * Author: 沧澜
 * Date: 2019-10-24
 */

namespace CalJect\Productivity\Components\DataProperty;

use CalJect\Productivity\Components\DataProperty\Exception\VerifyException;
use ReflectionException;
use ReflectionProperty;

/**
 * Class CallDataPropertyCheckEmpty
 * @package CalJect\Productivity\Components\DataProperty
 */
abstract class CallDataPropertyEmpty extends CallDataProperty
{
    /**
     * @param string $name
     * @param string $value
     * @return mixed|string
     * @throws ReflectionException
     * @throws VerifyException
     */
    public function _callGet(string $name, $value)
    {
        if (!$value) {
            $this->throwVerifyException(static::class, $name, (new ReflectionProperty($this, $name))->getDocComment());
        }
    }
    
}