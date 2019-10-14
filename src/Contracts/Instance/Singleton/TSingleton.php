<?php
/**
 * Author: 沧澜
 * Date: 2019-10-14
 */

namespace CalJect\Productivity\Contracts\Instance\Singleton;

/**
 * Trait TSingleton
 * @package CalJect\Productivity\Contracts\Instance\Singleton
 */
trait TSingleton
{
    /**
     * @return mixed
     */
    public static function get() {
        return static::getInstance();
    }
}