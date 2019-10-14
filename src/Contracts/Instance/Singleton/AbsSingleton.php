<?php
/**
 * Author: 沧澜
 * Date: 2019-10-14
 */

namespace CalJect\Productivity\Contracts\Instance\Singleton;

/**
 * Class AbsSingleton
 * @package CalJect\Productivity\Contracts\Instance\Singleton
 */
abstract class AbsSingleton
{
    /**
     * 静态单例集合
     * @var mixed[static::class]
     */
    private static $instances = [];
    
    /**
     * AbsSingleton constructor.
     */
    private function __construct()
    {
        $this->init();
    }
    
    /**
     * set clone to private
     */
    private function __clone()
    {
    
    }
    
    /**
     * @return static
     */
    public static function getInstance()
    {
        $instance = &self::$instances[static::class];
        if (!isset($instance)) {
            $instance = new static();
        }
        return $instance;
    }
    
    /**
     * AbsSingleton constructor init handle.
     * @return mixed
     */
    abstract protected function init();
}