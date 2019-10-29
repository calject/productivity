<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Contracts\DataProperty;

/**
 * Trait TCallDataPropertyCallOther
 * @package CalJect\Productivity\Contracts\DataProperty
 * ---------- method ----------
 * @method mixed __call($name, $arguments)
 */
trait TCallDataPropertyByName
{
    /**
     * @var bool
     */
    private $isCallOther = true;
    
    /**
     * @param string $name
     * @param $arguments
     * @return mixed
     */
    protected function _callOther(string $name, $arguments)
    {
        if (property_exists($this, $name)) {
            return $this->__call((isset($arguments[0]) ? 'set' : 'get') . ucfirst($name), $arguments);
        }
    }
}