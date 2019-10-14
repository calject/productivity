<?php
/**
 * Author: 沧澜
 * Date: 2019-10-14
 */

namespace CalJect\Productivity\Contracts\Instance\Singleton;

/**
 * Class AbsSingletonNoInit
 * @package CalJect\Productivity\Contracts\Instance\Singleton
 */
abstract class AbsSingletonNoInit extends AbsSingleton
{
    
    /**
     * AbsSingleton constructor init handle.
     * @return mixed
     */
    protected function init()
    {
    
    }
}