<?php
/**
 * Author: 沧澜
 * Date: 2019-10-14
 */

namespace CalJect\Productivity\Contracts\Instance\Multiton;

/**
 * Class AbsMultiton
 * @package CalJect\Productivity\Contracts\Instance\Multiton
 */
abstract class AbsMultiton
{
    /**
     * 静态多例集合
     * @var mixed[static::class][instance]
     */
    private static $instances = [];
    
    /**
     * AbsMultiton constructor.
     * @param $option
     */
    private function __construct($option)
    {
        $this->init($option);
    }
    
    /**
     * set clone to private
     */
    private function __clone()
    {
    
    }
    
    /**
     * 根据配置获取到当前类多例实例
     * @param mixed $option
     * @return static
     */
    public static function getInstance($option = NULL)
    {
        $instance = &self::$instances[static::class][$option];
        if (!isset($instance)) {
            $instance = new static($option);
        }
        return $instance;
    }
    
    /**
     * AbsMultiton constructor init handle.
     * @param mixed $option
     * @return mixed
     */
    abstract protected function init($option);
}