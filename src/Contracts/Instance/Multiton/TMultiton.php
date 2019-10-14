<?php
/**
 * Author: 沧澜
 * Date: 2019-10-14
 */

namespace CalJect\Productivity\Contracts\Instance\Multiton;

/**
 * Trait TMultiton
 * @package CalJect\Productivity\Contracts\Instance\Multiton
 */
trait TMultiton
{
    /**
     * 根据配置获取到当前类多例实例(适用于继承IMultiton接口类)
     * @param mixed $option
     * @return mixed
     */
    public static function get($option = NULL) {
        return static::getInstance($option);
    }
}