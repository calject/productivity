<?php
/**
 * Author: 沧澜
 * Date: 2019-09-03
 */

namespace CalJect\Productivity\Utils;


use Closure;
use Generator;

class GeneratorLoad
{
    
    const OPT_NO_INDEX = 0;
    const OPT_GET_INDEX = 1;
    
    /**
     * @param Generator $list
     * @param Closure $handle function($index, $value) {} when options = 1 or function($value) {}  when options = 0
     * @param int $options
     */
    final public static function each(Generator $list, Closure $handle, int $options = self::OPT_NO_INDEX)
    {
        foreach ($list as $index => $value) {
            $params = ($options & 1 === 1) ? [$index, $value] : [$value];
            $value instanceof Generator ? self::each($value, $handle) : call_user_func_array($handle, $params);
        }
    }
    
}