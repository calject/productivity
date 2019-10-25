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
abstract class CallDataPropertyCheckEmpty extends CallDataProperty
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
        if (empty($value)) {
            $refPro = new ReflectionProperty($this, $name);
            if ($docComment = $refPro->getDocComment()) {
                $match = preg_match('/@note(.*)\\n/', $docComment, $arr);
                if ($match && isset($arr[1]) && $note = trim($arr[1])) {
                    VerifyException::throw("{$note}[$name] 字段不能为空", 422);
                }
            }
            VerifyException::throw(basename(str_replace('\\', '/', static::class)) . "::$name 不能为空", 422);
        }
    }
    
}