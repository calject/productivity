<?php
/**
 * Author: 沧澜
 * Date: 2019-09-20
 */

namespace CalJect\Productivity\Components\Criteria\Branch;


use CalJect\Productivity\Exceptions\ClosureRunException;
use CalJect\Productivity\Utils\ClosureUtil;
use Closure;

class SwControl
{
    /**
     * @var mixed
     */
    protected $checkValue;
    
    /**
     * @var Closure
     */
    protected $default;
    
    /**
     * @var array
     */
    protected $binds = [];
    
    /**
     * @var mixed
     */
    protected $data;
    
    /*---------------------------------------------- call ----------------------------------------------*/
    /**
     * @return mixed
     * @throws ClosureRunException
     */
    public function callDefault()
    {
        if (isset($this->default)) {
            return ClosureUtil::checkClosureWithExec($this->default, [$this]);
        } else {
            return $this->checkValue;
        }
    }
    
    /**
     * @param int|string $key
     * @param Closure $noFoundHandle
     * @return mixed
     * @throws ClosureRunException
     */
    public function call($key, Closure $noFoundHandle = null)
    {
        if (isset($this->binds[$key])) {
            return ClosureUtil::checkClosureWithExec($this->binds[$key], [$this]);
        } else {
            return $noFoundHandle ? ClosureUtil::checkClosureWithExec($noFoundHandle, [$this]) : null;
        }
    }
    
    /**
     * @param int|string $key
     * @return mixed
     * @throws ClosureRunException
     */
    public function callInDefault($key)
    {
        if (isset($this->binds[$key])) {
            return ClosureUtil::checkClosureWithExec($this->binds[$key], [$this]);
        } else {
            return $this->callDefault();
        }
    }
    
    /**
     * @return Closure
     */
    public static function CLOSURE_DEFAULT(): Closure
    {
        return function (SwControl $control) {
            return $control->callDefault();
        };
    }
    
    /**
     * @param string|int $key
     * @return Closure
     */
    public static function CLOSURE_BIND($key): Closure
    {
        return function (SwControl $control) use ($key) {
            return $control->callInDefault($key);
        };
    }
    /*---------------------------------------------- check ----------------------------------------------*/
    
    /**
     * @param string|int $key
     * @return bool
     */
    public function has($key): bool
    {
        return isset($this->binds[$key]);
    }
    
    /*---------------------------------------------- set/get ----------------------------------------------*/
    
    /**
     * @param mixed $checkValue
     * @return $this
     */
    public function setCheckValue($checkValue)
    {
        $this->checkValue = $checkValue;
        return $this;
    }
    
    /**
     * @param Closure $default
     * @return $this
     */
    public function setDefault(Closure $default)
    {
        $this->default = $default;
        return $this;
    }
    
    /**
     * @param array $binds
     * @return $this
     */
    public function setBinds(array $binds)
    {
        $this->binds = $binds;
        return $this;
    }
    
    /**
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public function appendBind($key, $value)
    {
        $this->binds[$key] = $value;
        return $this;
    }
    
    /**
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    
    /*---------------------------------------------- get ----------------------------------------------*/
    
    /**
     * @return mixed
     */
    public function getCheckValue()
    {
        return $this->checkValue;
    }
    
    /**
     * @return Closure
     */
    public function getDefault(): Closure
    {
        return $this->default;
    }
    
    /**
     * @return array
     */
    public function getBinds(): array
    {
        return $this->binds;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}