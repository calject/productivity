<?php
/**
 * Author: 沧澜
 * Date: 2019-10-28
 */

namespace CalJect\Productivity\Contracts\DataProperty;

/**
 * Trait TCallDataProperty
 * @package CalJect\Productivity\Contracts\DataProperty
 * ---------- property ----------
 * @property bool $isCallSet
 * @property bool $isCallGet
 * @property bool $isCallOther
 *
 * ---------- method ----------
 * @method mixed _callSet(string $name, $arguments)
 * @method mixed _callGet(string $name, $value)
 * @method mixed _callOther(string $name, $arguments)
 */
trait TCallDataProperty
{
    /**
     * @param string $name
     * @param mixed $arguments
     * @return $this|mixed
     */
    public function __call($name, $arguments)
    {
        $method = substr($name, 0, 3);
        $property = lcfirst(substr($name, 3));
        if ($method == 'set') {
            $_ = $this->isCallSet ?? method_exists($this, '_callSet') ? $this->_callSet($property, $arguments) : ($this->{$property} = $arguments[0] ?? null);
            return $this;
        } elseif ($method == 'get') {
            return $this->isCallGet ?? method_exists($this, '_callGet') ? $this->_callGet($property, $this->{$property}) : $this->{$property};
        } else {
            if ($this->isCallOther ?? method_exists($this, '_callOther')) {
                return $this->_callOther($name, $arguments);
            }
        }
    }
}