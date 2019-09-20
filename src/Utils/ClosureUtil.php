<?php
/**
 * Author: 沧澜
 * Date: 2019-09-20
 */

namespace CalJect\Productivity\Utils;


use CalJect\Productivity\Exceptions\ClosureRunException;
use Closure;

class ClosureUtil
{
    
    /**
     * 检查的闭包函数或执行类 并执行回调
     * @param Closure|string|array $closure 检查的闭包函数或执行类(string/array)
     * @param array $params                 执行参数
     * @param bool $isThrowException        是否抛出异常
     * @param null $default                 非异常返回的默认值
     * @return mixed
     * @throws ClosureRunException
     */
    final public static function checkClosureWithExec($closure, array $params = [], $isThrowException = true, $default = null)
    {
        if ($closure instanceof Closure) {
            return call_user_func_array($closure, $params);
        } else if (is_array($closure) && count($closure) >= 2) {
            return call_user_func_array(array_slice($closure, 0, 2), $params);
        } else if (is_string($closure) && count(explode("::", $closure)) == 2) {
            return self::checkClosureWithExec(explode("::", $closure), $params);
        } else {
            $isThrowException && ClosureRunException::throw('can not run closure in ' . gettype($closure) . ' [type error].');
            return $default;
        }
    }
    
}