<?php
/**
 * Author: 沧澜
 * Date: 2019-10-23
 */

namespace CalJect\Productivity\Components\DataProperty;

use Closure;

/**
 * Class CallDataProperty
 * @package CalJect\Productivity\Components\DataProperty
 * @method mixed _callSet(string $name, $arguments)
 * @method mixed _callGet(string $name, $value)
 * @method mixed _callOther(string $name, $arguments)
 */
abstract class CallDataProperty
{
    /**
     * @var bool
     */
    private $isCallSet, $isCallGet, $isCallOther = false;
    
    /**
     * CallDataProperty constructor.
     */
    public function __construct()
    {
        $this->isCallSet = method_exists($this, '_callSet');
        $this->isCallGet = method_exists($this, '_callGet');
        $this->isCallOther = method_exists($this, '_callOther');
        $this->_init();
    }
    // class create init
    protected function _init() { }
    
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
            $_ = $this->isCallSet ? $this->_callSet($property, $arguments) : ($this->{$property} = $arguments[0] ?? null);
            return $this;
        } elseif ($method == 'get') {
            return $this->isCallGet ? $this->_callGet($property, $this->{$property}) : $this->{$property};
        } else {
            if ($this->isCallOther) {
                return $this->_callOther($name, $arguments);
            }
        }
    }
    
    /**
     * @param array $data
     * @param null $default
     * @return $this
     */
    public function setInArray(array $data, $default = null)
    {
        foreach ($this as $property => $value) {
            $this->{$property} = $data[$property] ?? $this->{$property} ?? $default;
        }
        return $this;
    }
    
    /**
     * @param Closure $handle
     * @param array $data
     * @return $this
     */
    public function setMap(Closure $handle, array $data)
    {
        foreach ($this as $property => $value) {
            call_user_func_array($handle,[$this, $property, $data[$property] ?? null, $value]);
        }
        return $this;
    }
}